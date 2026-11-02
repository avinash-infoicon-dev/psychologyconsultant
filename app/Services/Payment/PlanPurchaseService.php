<?php

namespace App\Services\Payment;

use App\Models\Credit;
use App\Models\ExchangeTransaction;
use App\Models\Plan;
use App\Models\User;
use App\Services\Messaging\ExchangeLedgerService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Website / dummy checkout for §6.2 hybrid model:
 * - monthly subscription tiers → SubscriptionService
 * - on-demand + additional exchange packs → credit ledger (does not cancel an active sub)
 */
class PlanPurchaseService
{
    public function __construct(
        protected SubscriptionService $subscriptions,
        protected ExchangeLedgerService $ledger,
    ) {}

    /**
     * Dummy payment always succeeds and grants access immediately.
     *
     * @param  array{stripe_subscription_id?: string}  $options
     *
     * @throws RuntimeException
     */
    public function purchase(User $user, Plan $plan, string $gateway = 'dummy', array $options = []): mixed
    {
        $patient = $user->patient;

        if ($patient === null) {
            throw new RuntimeException('Only patients can purchase a plan.');
        }

        if (! $plan->is_active) {
            throw new RuntimeException('This plan is not available.');
        }

        // Monthly (or other period) subscription tiers.
        if ($plan->plan_type === Plan::TYPE_SUBSCRIPTION && $plan->billing_interval !== 'per_exchange') {
            // Twint branch creates a local active subscription without Stripe.
            // Old gateway call (kept for reference when real payments land):
            // return $this->subscriptions->subscribe($user, $plan, $gateway === 'stripe' ? 'stripe' : 'twint', $paymentMethodId);

            // return $this->subscriptions->subscribe($user, $plan, 'twint');
            return $this->subscriptions->subscribe(
                $user,
                $plan,
                $gateway,
                null,
                $options['stripe_subscription_id'] ?? null,
            );
        }

        // On-demand / additional exchange → award credits (quota overflow & try-before-subscribe).
        return DB::transaction(function () use ($patient, $plan, $gateway) {
            $amount = max(1, (int) $plan->exchange_quota);

            $subscription = $patient->lifecycleSubscription();
            // Legacy: extra packs only attached to status=active.
            // $subscription = $patient->activeSubscription();
            if ($subscription) {
                $subscription->increment('purchased_exchanges', $amount);
                $subscription->refresh();
                $subscription->forceFill([
                    'exchanges_remaining' => $subscription->remaining_exchanges,
                ])->save();
                $this->ledger->recordPurchased(
                    $subscription,
                    $amount,
                    sprintf('Purchase of plan "%s"', $plan->slug),
                );
            } else {
                $this->ledger->record([
                    'patient_id' => $patient->id,
                    'type' => ExchangeTransaction::TYPE_PURCHASED,
                    'quantity' => $amount,
                    'reason' => sprintf('Purchase of plan "%s"', $plan->slug),
                ]);
            }

            return Credit::create([
                'patient_id' => $patient->id,
                'plan_id' => $plan->id,
                'amount' => $amount,
                'source' => 'purchase',
                'note' => sprintf(
                    'Dummy %s payment for plan "%s" (%s)',
                    $gateway,
                    $plan->slug,
                    $plan->name,
                ),
                'expires_at' => now()->addYear(),
            ]);
        });
    }
}
