<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Notifications\SubscriptionNotification;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Despite the name (kept to match the original module layout), this
 * listener doesn't create the invoice — InvoiceService already did that
 * when the subscription was created, and PaymentService marks it paid
 * directly on confirm(). What's left to do here once a payment
 * completes is notify the user their payment succeeded.
 */
class CreateInvoice implements ShouldQueue
{
    public function __construct(protected NotificationService $notifications) {}

    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment;

        if (! $payment->subscription) {
            return;
        }

        $payload = SubscriptionNotification::for($payment->subscription, 'renewed');

        $this->notifications->send(
            $payment->user,
            $payload['type'],
            $payload['title'],
            $payload['body'],
            $payload['data'],
        );
    }
}
