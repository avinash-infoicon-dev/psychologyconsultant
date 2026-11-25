<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Notifications\PaymentNotification;
use App\Services\NotificationService;

class NotifyPaymentFailed
{
    public function __construct(protected NotificationService $notifications) {}

    public function handle(PaymentFailed $event): void
    {
        $payment = $event->payment;
        $payment->loadMissing('user');

        if (! $payment->user) {
            return;
        }

        $this->notifications->sendPayload(
            $payment->user,
            PaymentNotification::failed($payment),
            'payment:'.$payment->id.':failed',
        );
    }
}
