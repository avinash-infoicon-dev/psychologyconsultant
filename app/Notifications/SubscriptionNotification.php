<?php

namespace App\Notifications;

use App\Models\Subscription;

class SubscriptionNotification
{
    public static function for(Subscription $subscription, string $event): array
    {
        $subscription->loadMissing('plan');
        $planName = $subscription->plan->name ?? '';

        $map = [
            'created' => [
                'type' => 'subscription',
                'title' => __('frontend.notifications.subscription_created_title'),
                'body' => __('frontend.notifications.subscription_created_body', ['plan' => $planName]),
            ],
            'renewed' => [
                'type' => 'subscription',
                'title' => __('frontend.notifications.subscription_renewed_title'),
                'body' => __('frontend.notifications.subscription_renewed_body', ['plan' => $planName]),
            ],
            'cancelled' => [
                'type' => 'subscription',
                'title' => __('frontend.notifications.subscription_cancelled_title'),
                'body' => __('frontend.notifications.subscription_cancelled_body', ['plan' => $planName]),
            ],
            'payment_failed' => [
                'type' => 'payment_failed',
                'title' => __('frontend.notifications.payment_failed_title'),
                'body' => __('frontend.notifications.payment_failed_body'),
            ],
            'expired' => [
                'type' => 'subscription_expired',
                'title' => __('frontend.notifications.subscription_expired_title'),
                'body' => __('frontend.notifications.subscription_expired_body', ['plan' => $planName]),
            ],
            'renewal_approaching' => [
                'type' => 'subscription_renewal',
                'title' => __('frontend.notifications.subscription_renewal_title'),
                'body' => __('frontend.notifications.subscription_renewal_body', ['plan' => $planName]),
            ],
        ];

        $resolved = $map[$event] ?? [
            'type' => 'subscription',
            'title' => __('frontend.notifications.subscription_update_title'),
            'body' => __('frontend.notifications.subscription_update_body', ['status' => $subscription->status]),
        ];

        return [
            'type' => $resolved['type'],
            'title' => $resolved['title'],
            'body' => $resolved['body'],
            'data' => [
                'subscription_id' => $subscription->id,
                'event' => $event,
                'url' => route('frontend.patient.subscription.index'),
            ],
        ];
    }
}
