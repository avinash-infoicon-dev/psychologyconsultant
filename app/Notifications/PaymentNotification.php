<?php

namespace App\Notifications;

use App\Models\Payment;

class PaymentNotification
{
    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function succeeded(Payment $payment): array
    {
        return [
            'type' => 'payment',
            'title' => __('frontend.notifications.payment_success_title'),
            'body' => __('frontend.notifications.payment_success_body'),
            'data' => [
                'payment_id' => $payment->id,
                'invoice_id' => $payment->invoice_id,
                'url' => route('frontend.patient.subscription.history', [], false),
            ],
        ];
    }

    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function failed(Payment $payment): array
    {
        return [
            'type' => 'payment_failed',
            'title' => __('frontend.notifications.payment_attempt_failed_title'),
            'body' => __('frontend.notifications.payment_attempt_failed_body'),
            'data' => [
                'payment_id' => $payment->id,
                'invoice_id' => $payment->invoice_id,
                'url' => route('frontend.patient.subscription.history', [], false),
            ],
        ];
    }
}
