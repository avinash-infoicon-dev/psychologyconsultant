<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Notifications\PaymentNotification;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyPaymentFailed implements ShouldQueue
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
