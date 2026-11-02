<?php

namespace App\Listeners;

use App\Events\SubscriptionCreated;
use App\Notifications\SubscriptionNotification;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateQuota implements ShouldQueue
{
    public function __construct(protected NotificationService $notifications) {}

    public function handle(SubscriptionCreated $event): void
    {
        $subscription = $event->subscription;

        // Quota fields (exchanges_remaining/exchanges_used) are already
        // set correctly at creation time by SubscriptionService — this
        // listener's job is the resulting user-facing notification.
        $payload = SubscriptionNotification::for($subscription, 'created');

        $this->notifications->send(
            $subscription->patient->user,
            $payload['type'],
            $payload['title'],
            $payload['body'],
            $payload['data'],
        );
    }
}
