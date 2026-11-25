<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Notifications\PaymentNotification;
use App\Notifications\SubscriptionNotification;
use App\Services\NotificationService;

/**
 * The legacy listener name is retained for compatibility with its event
 * registration; invoices are created by InvoiceService.
 */
class CreateInvoice
{
    public function __construct(protected NotificationService $notifications) {}

    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment;
        $payment->loadMissing(['user', 'subscription.plan']);

        if (! $payment->user) {
            return;
        }

        if ($payment->subscription) {
            $periodKey = $payment->subscription->current_period_ends_at?->toDateString() ?? 'unknown';
            $this->notifications->sendPayload(
                $payment->user,
                SubscriptionNotification::for($payment->subscription, 'renewed'),
                'subscription:'.$payment->subscription->id.':renewed:'.$periodKey,
            );

            return;
        }

        $this->notifications->sendPayload(
            $payment->user,
            PaymentNotification::succeeded($payment),
            'payment:'.$payment->id.':succeeded',
        );
    }
}
