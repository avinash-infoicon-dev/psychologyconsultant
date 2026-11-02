<?php

namespace App\Http\Controllers\API\Payment;

use App\Events\PaymentCompleted;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\Payment\PlanPurchaseService;
use App\Services\Payment\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptions,
        protected PlanPurchaseService $purchases,
    ) {}

    /**
     * Signature verification already happened in VerifyWebhookSignature
     * middleware before this method runs — this only needs to react to
     * the event payload.
     */
    public function stripe(Request $request): JsonResponse
    {
        $event = $request->input('type');
        $object = $request->input('data.object', []);

        match ($event) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($object),
            'payment_intent.succeeded' => $this->handleStripePaymentSucceeded($object),
            'payment_intent.payment_failed' => $this->handleStripePaymentFailed($object),
            'invoice.paid', 'invoice.payment_succeeded' => $this->handleStripeInvoicePaid($object),
            'invoice.payment_failed' => $this->handleStripeSubscriptionPaymentFailed($object),
            'customer.subscription.updated' => $this->handleStripeSubscriptionUpdated($object),
            'customer.subscription.deleted' => $this->handleStripeSubscriptionCancelled($object),
            default => Log::info('Unhandled Stripe webhook event', ['type' => $event]),
        };

        return response()->json(['received' => true]);
    }

    public function twint(Request $request): JsonResponse
    {
        $status = $request->input('status');
        $transactionId = $request->input('transactionId');

        $payment = Payment::where('gateway_transaction_id', $transactionId)->first();

        if (! $payment) {
            Log::warning('Twint webhook for unknown payment', ['transaction_id' => $transactionId]);

            return response()->json(['received' => true]);
        }

        if (in_array($status, ['SUCCESS', 'settled'], true) && $payment->status !== 'succeeded') {
            $payment->update(['status' => 'succeeded', 'paid_at' => now(), 'gateway_response' => $request->all()]);
            $payment->invoice?->update(['status' => 'paid', 'paid_at' => now()]);
            event(new PaymentCompleted($payment));
        } elseif (in_array($status, ['FAILED', 'CANCELLED'], true)) {
            $payment->update(['status' => 'failed', 'failure_reason' => $status, 'gateway_response' => $request->all()]);
        }

        return response()->json(['received' => true]);
    }

    protected function handleCheckoutSessionCompleted(array $object): void
    {
        if (($object['payment_status'] ?? null) !== 'paid') {
            return;
        }

        $userId = $object['metadata']['user_id'] ?? null;
        $planId = $object['metadata']['plan_id'] ?? null;
        $intent = $object['metadata']['intent'] ?? 'subscribe';
        $stripeSubscriptionId = is_string($object['subscription'] ?? null)
            ? $object['subscription']
            : ($object['subscription']['id'] ?? null);

        if (! $userId || ! $planId) {
            return;
        }

        $user = User::find($userId);
        $plan = Plan::find($planId);

        if (! $user || ! $plan) {
            return;
        }

        if ($stripeSubscriptionId && Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->exists()) {
            return;
        }

        try {
            if ($intent === 'change') {
                $subscription = $user->patient?->lifecycleSubscription();
                if ($subscription) {
                    $this->subscriptions->changePlan($subscription, $plan);
                    if ($stripeSubscriptionId && ! $subscription->stripe_subscription_id) {
                        $subscription->update(['stripe_subscription_id' => $stripeSubscriptionId]);
                    }
                }

                return;
            }

            $this->purchases->purchase($user, $plan, 'stripe', [
                'stripe_subscription_id' => $stripeSubscriptionId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Stripe checkout.session.completed fulfillment failed', [
                'error' => $e->getMessage(),
                'session_id' => $object['id'] ?? null,
            ]);
        }
    }

    protected function handleStripePaymentSucceeded(array $object): void
    {
        $payment = Payment::where('gateway_transaction_id', $object['id'] ?? null)->first();

        if (! $payment || $payment->status === 'succeeded') {
            return;
        }

        $payment->update(['status' => 'succeeded', 'paid_at' => now(), 'gateway_response' => $object]);
        $payment->invoice?->update(['status' => 'paid', 'paid_at' => now()]);

        event(new PaymentCompleted($payment));
    }

    protected function handleStripePaymentFailed(array $object): void
    {
        Payment::where('gateway_transaction_id', $object['id'] ?? null)->first()?->update([
            'status' => 'failed',
            'failure_reason' => $object['last_payment_error']['message'] ?? 'Payment failed',
            'gateway_response' => $object,
        ]);
    }

    /**
     * Recurring Stripe invoices: first cycle is already granted at checkout;
     * later cycles reset the local quota / period dates.
     */
    protected function handleStripeInvoicePaid(array $object): void
    {
        $subscriptionId = $object['subscription']
            ?? $object['parent']['subscription_details']['subscription']
            ?? null;

        if (is_array($subscriptionId)) {
            $subscriptionId = $subscriptionId['id'] ?? null;
        }

        if (! $subscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();

        if (! $subscription) {
            return;
        }

        $reason = $object['billing_reason'] ?? '';

        if ($reason === 'subscription_cycle') {
            $this->subscriptions->renew($subscription);

            return;
        }

        $this->subscriptions->syncFromStripeSubscription(
            $subscription,
            $this->subscriptionPayloadFromInvoice($object, $subscriptionId),
        );
    }

    protected function handleStripeSubscriptionUpdated(array $object): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $object['id'] ?? null)->first();

        if (! $subscription) {
            return;
        }

        $this->subscriptions->syncFromStripeSubscription($subscription, $object);
    }

    protected function handleStripeSubscriptionPaymentFailed(array $object): void
    {
        $subscriptionId = $object['subscription'] ?? null;
        $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();

        // Legacy: status only.
        // $subscription?->update(['status' => 'past_due']);
        if ($subscription) {
            $this->subscriptions->markPaymentFailed(
                $subscription,
                $object['last_finalization_error']['message'] ?? 'Payment failed',
            );
        }
    }

    protected function handleStripeSubscriptionCancelled(array $object): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $object['id'] ?? null)->first();

        if (! $subscription || $subscription->isCancelled()) {
            return;
        }

        // Local cancel already ran; this keeps Stripe-initiated cancels in sync.
        $subscription->update([
            'status' => Subscription::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'ends_at' => now(),
            'auto_renew' => false,
            'cancel_at_period_end' => false,
        ]);
    }

    protected function subscriptionPayloadFromInvoice(array $invoice, string $subscriptionId): array
    {
        $period = $invoice['lines']['data'][0]['period'] ?? [];

        return [
            'id' => $subscriptionId,
            'status' => 'active',
            'cancel_at_period_end' => false,
            'current_period_start' => $period['start'] ?? null,
            'current_period_end' => $period['end'] ?? null,
        ];
    }
}
