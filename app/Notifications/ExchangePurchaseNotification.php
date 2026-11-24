<?php

namespace App\Notifications;

use App\Models\Credit;
use App\Models\Plan;

class ExchangePurchaseNotification
{
    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function purchased(Plan $plan, Credit $credit): array
    {
        return [
            'type' => 'payment',
            'title' => __('frontend.notifications.payment_success_title'),
            'body' => __('frontend.notifications.exchange_purchase_body', [
                'count' => $credit->amount,
                'plan' => $plan->name,
            ]),
            'data' => [
                'credit_id' => $credit->id,
                'plan_id' => $plan->id,
                'quantity' => $credit->amount,
                'url' => route('frontend.patient.subscription.index', [], false),
            ],
        ];
    }
}
