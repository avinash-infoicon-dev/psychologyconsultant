<?php

namespace App\Services\Payment;

use App\Events\SubscriptionCreated;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Messaging\ExchangeLedgerService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SubscriptionService
{
    public function __construct(
        protected StripeService $stripe,
        protected TwintService $twint,
        protected InvoiceService $invoices,
        protected ExchangeLedgerService $ledger,
    ) {}

    /**
     * Twint has no native "subscription" concept (it's a payment method,
     * not a billing platform) — for Twint we create a local subscription
     * immediately and charge each period manually via a scheduled job,
     * rather than relying on gateway-side recurring billing like Stripe.
     */
    public function subscribe(User $patientUser, Plan $plan, string $gateway, ?string $paymentMethodId = null, ?string $existingStripeSubscriptionId = null): Subscription
    {
        $patient = $patientUser->patient;

        abort_if($patient === null, 422, 'Only patients can subscribe to a plan.');

        return DB::transaction(function () use ($patientUser, $patient, $plan, $gateway, $paymentMethodId, $existingStripeSubscriptionId) {
            $this->cancelExistingActiveSubscription($patient);

            $stripeSubscriptionId = $existingStripeSubscriptionId;

            //  if ($gateway === 'stripe') {
            //     if (! $plan->stripe_price_id) {
            //         throw new RuntimeException("Plan '{$plan->slug}' has no stripe_price_id configured.");
            //     }

            //     $customer = $this->stripe->createCustomer($patientUser->email, $patientUser->name);
            //     $stripeSub = $this->stripe->createSubscription($customer['id'], $plan->stripe_price_id, [
            //         'patient_id' => $patient->id,
            //     ]);
            //     $stripeSubscriptionId = $stripeSub['id'];
            // }
            // Checkout Session already created the Stripe subscription — reuse that id.
            // Creating another one here left cancel/auto-renew pointing at the wrong Stripe sub.
            if (! $stripeSubscriptionId && $gateway === 'stripe' && $plan->stripe_price_id) {
                $customer = $this->stripe->createCustomer($patientUser->email, $patientUser->name);
                $stripeSub = $this->stripe->createSubscription($customer['id'], $plan->stripe_price_id, [
                    'patient_id' => $patient->id,
                ]);
                $stripeSubscriptionId = $stripeSub['id'] ?? null;
            }

            $subscription = Subscription::create([
                'patient_id' => $patient->id,
                'plan_id' => $plan->id,
                'plan_snapshot' => $plan->toSnapshot(),
                'status' => 'active',
                'starts_at' => now(),
                'current_period_starts_at' => now(),
                'current_period_ends_at' => $this->periodEnd($plan),
                'stripe_subscription_id' => $stripeSubscriptionId,
                'included_exchanges' => $plan->exchange_quota,
                'consumed_exchanges' => 0,
                'purchased_exchanges' => max(0, (int) $patient->creditBalance()),
                'exchanges_used' => 0,
                'exchanges_remaining' => $plan->exchange_quota + max(0, (int) $patient->creditBalance()),
                'auto_renew' => true,
                'cancel_at_period_end' => false,
                'pending_plan_id' => null,
                'last_payment_failed_at' => null,
                'last_payment_error' => null,
            ]);

            $this->invoices->generateForSubscription($subscription);

            if ((int) $subscription->included_exchanges > 0) {
                $this->ledger->recordIncluded(
                    $subscription,
                    (int) $subscription->included_exchanges,
                    'Subscription included allowance',
                );
            }
            if ((int) $subscription->purchased_exchanges > 0) {
                $this->ledger->recordPurchased(
                    $subscription,
                    (int) $subscription->purchased_exchanges,
                    'Rolled additional exchanges onto subscription',
                );
            }

            event(new SubscriptionCreated($subscription));

            return $subscription;
        });
    }

    public function cancel(Subscription $subscription): void
    {
        if ($subscription->stripe_subscription_id) {
            $this->stripe->cancelSubscription($subscription->stripe_subscription_id);
        }

        // No refund — access ends immediately for this dummy/local flow.
        $subscription->update([
            'status' => Subscription::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'ends_at' => now(),
            'auto_renew' => false,
            'cancel_at_period_end' => false,
            'pending_plan_id' => null,
        ]);
    }

    public function setAutoRenew(Subscription $subscription, bool $enabled): void
    {
        // Local-only (dummy / Twint without a Stripe subscription):
        // $subscription->update([
        //     'auto_renew' => $enabled,
        //     'cancel_at_period_end' => ! $enabled,
        // ]);

        if ($subscription->stripe_subscription_id) {
            $this->stripe->updateSubscription($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => ! $enabled,
            ]);
        }

        $subscription->update([
            'auto_renew' => $enabled,
            'cancel_at_period_end' => ! $enabled,
        ]);
    }

    /**
     * Stripe webhook: keep local cancel_at_period_end / period dates in sync
     * without charging again.
     */
    public function syncFromStripeSubscription(Subscription $subscription, array $stripeSubscription): Subscription
    {
        $cancelAtPeriodEnd = (bool) ($stripeSubscription['cancel_at_period_end'] ?? false);
        $status = (string) ($stripeSubscription['status'] ?? '');
        $periodStart = $this->timestampToCarbon($stripeSubscription['current_period_start'] ?? null);
        $periodEnd = $this->timestampToCarbon($stripeSubscription['current_period_end'] ?? null);

        $localStatus = match ($status) {
            'canceled', 'cancelled' => Subscription::STATUS_CANCELLED,
            'unpaid', 'past_due', 'incomplete', 'incomplete_expired' => Subscription::STATUS_PAST_DUE,
            'trialing' => Subscription::STATUS_TRIALING,
            default => Subscription::STATUS_ACTIVE,
        };

        $payload = [
            'auto_renew' => ! $cancelAtPeriodEnd && ! in_array($localStatus, [Subscription::STATUS_CANCELLED, Subscription::STATUS_EXPIRED], true),
            'cancel_at_period_end' => $cancelAtPeriodEnd,
            'status' => $localStatus,
        ];

        if ($periodStart) {
            $payload['current_period_starts_at'] = $periodStart;
        }
        if ($periodEnd) {
            $payload['current_period_ends_at'] = $periodEnd;
        }

        if ($localStatus === Subscription::STATUS_CANCELLED) {
            $payload['cancelled_at'] = $payload['cancelled_at'] ?? now();
            $payload['ends_at'] = $periodEnd ?? now();
            $payload['auto_renew'] = false;
        }

        $subscription->update($payload);

        return $subscription->fresh(['plan']);
    }

    /**
     * Immediate plan switch (dummy checkout). No refund on upgrade or downgrade.
     * Consumed / purchased exchanges are kept; included quota follows the new plan.
     */
    public function changePlan(Subscription $subscription, Plan $plan): Subscription
    {
        if (! $plan->isSubscription() || $plan->billing_interval === 'per_exchange') {
            throw new RuntimeException('This plan cannot replace the current subscription.');
        }

        if ((int) $plan->id === (int) $subscription->plan_id) {
            return $subscription;
        }

        $subscription->loadMissing('plan');
        $oldIncluded = (int) $subscription->included_exchanges;
        $included = (int) $plan->exchange_quota;
        $purchased = (int) $subscription->purchased_exchanges;
        $consumed = (int) $subscription->consumed_exchanges;
        $delta = $included - $oldIncluded;

        $subscription->update([
            'plan_id' => $plan->id,
            'plan_snapshot' => $plan->toSnapshot(),
            'pending_plan_id' => null,
            'included_exchanges' => $included,
            'exchanges_remaining' => max(0, $included + $purchased - $consumed),
            'status' => $subscription->status === Subscription::STATUS_PAST_DUE
                ? Subscription::STATUS_ACTIVE
                : $subscription->status,
            'last_payment_failed_at' => null,
            'last_payment_error' => null,
        ]);

        if ($delta !== 0) {
            $this->ledger->recordIncluded(
                $subscription,
                $delta,
                'Plan change included allowance',
            );
        }

        return $subscription->fresh(['plan']);
    }

    public function markPaymentFailed(Subscription $subscription, ?string $message = null): void
    {
        $subscription->update([
            'status' => Subscription::STATUS_PAST_DUE,
            'last_payment_failed_at' => now(),
            'last_payment_error' => $message,
        ]);

        $subscription->loadMissing(['patient.user', 'plan']);
        $patientUser = $subscription->patient?->user;

        if ($patientUser) {
            $payload = \App\Notifications\SubscriptionNotification::for($subscription, 'payment_failed');
            app(\App\Services\NotificationService::class)->sendPayload($patientUser, $payload);
        }

        $patientName = $patientUser?->name ?? '#'.$subscription->patient_id;
        app(\App\Services\NotificationService::class)->notifyAdmins(
            \App\Notifications\AdminAlertNotification::systemAlert(
                __('frontend.notifications.admin_payment_failed_title'),
                __('frontend.notifications.admin_payment_failed_body', ['name' => $patientName]),
                route('admin.subscriptions.index'),
                ['subscription_id' => $subscription->id],
            ),
        );
    }

    /** Dummy retry after a failed charge. Real gateway retry comes later. */
    public function retryPayment(Subscription $subscription): Subscription
    {
        $subscription->update([
            'status' => Subscription::STATUS_ACTIVE,
            'last_payment_failed_at' => null,
            'last_payment_error' => null,
        ]);

        return $subscription->fresh(['plan']);
    }

    /** Called by a scheduled job at the start of each billing cycle for Twint subscribers. */
    public function renew(Subscription $subscription): void
    {
        $subscription->loadMissing(['plan', 'pendingPlan']);

        if (! $subscription->auto_renew || $subscription->cancel_at_period_end) {
            $subscription->update([
                'status' => Subscription::STATUS_EXPIRED,
                'ends_at' => $subscription->ends_at ?? now(),
                'pending_plan_id' => null,
            ]);

            $subscription->loadMissing(['patient.user', 'plan']);
            if ($subscription->patient?->user) {
                $payload = \App\Notifications\SubscriptionNotification::for($subscription, 'expired');
                app(\App\Services\NotificationService::class)->sendPayload($subscription->patient->user, $payload);
            }

            return;
        }

        if ($subscription->pending_plan_id && $subscription->pendingPlan) {
            $this->changePlan($subscription, $subscription->pendingPlan);
            $subscription->refresh();
            $subscription->loadMissing('plan');
        }

        $included = (int) $subscription->included_exchanges;
        $purchased = (int) $subscription->purchased_exchanges;
        $consumed = (int) $subscription->consumed_exchanges;
        // Unused included allowance is discarded; unused purchased packs roll over.
        $purchasedUsed = max(0, $consumed - $included);
        $purchasedRemaining = max(0, $purchased - $purchasedUsed);
        $expiredIncluded = max(0, $included - min($consumed, $included));
        $newIncluded = (int) $subscription->plan->exchange_quota;

        $subscription->update([
            'status' => Subscription::STATUS_ACTIVE,
            'current_period_starts_at' => now(),
            'current_period_ends_at' => $this->periodEnd($subscription->plan),
            'included_exchanges' => $newIncluded,
            'consumed_exchanges' => 0,
            'purchased_exchanges' => $purchasedRemaining,
            // Legacy columns kept in sync for older screens.
            'exchanges_used' => 0,
            'exchanges_remaining' => $newIncluded + $purchasedRemaining,
            'last_payment_failed_at' => null,
            'last_payment_error' => null,
        ]);

        $this->ledger->recordRenewal(
            $subscription,
            $newIncluded,
            $expiredIncluded,
            'Billing period renewal',
        );

        $this->invoices->generateForSubscription($subscription);
    }

    protected function cancelExistingActiveSubscription(Patient $patient): void
    {
        $existing = $patient->lifecycleSubscription();
        // Legacy: only cancelled a strictly active row.
        // $existing = $patient->activeSubscription();

        if ($existing) {
            $this->cancel($existing);
        }
    }

    protected function periodEnd(Plan $plan): \Illuminate\Support\Carbon
    {
        return match ($plan->billing_interval) {
            'yearly' => now()->addYear(),
            'quarterly' => now()->addMonths(3),
            // On-demand / additional exchange packs (§6.3) — valid for one year from purchase.
            'per_exchange' => now()->addYear(),
            default => now()->addMonth(),
        };
    }

    protected function timestampToCarbon(mixed $timestamp): ?\Illuminate\Support\Carbon
    {
        if (! is_numeric($timestamp)) {
            return null;
        }

        return \Illuminate\Support\Carbon::createFromTimestamp((int) $timestamp);
    }
}
