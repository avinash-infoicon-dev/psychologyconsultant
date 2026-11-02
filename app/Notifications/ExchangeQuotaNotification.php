<?php

namespace App\Notifications;

use App\Models\Subscription;

class ExchangeQuotaNotification
{
    /**
     * @return array{type: string, title: string, body: string, data: array<string, mixed>}
     */
    public static function low(Subscription $subscription, int $remaining): array
    {
        return [
            'type' => 'exchange_quota_low',
            'title' => __('frontend.notifications.quota_low_title'),
            'body' => __('frontend.notifications.quota_low_body', ['count' => $remaining]),
            'data' => [
                'subscription_id' => $subscription->id,
                'remaining' => $remaining,
                'url' => route('frontend.patient.subscription.index'),
            ],
        ];
    }
}
