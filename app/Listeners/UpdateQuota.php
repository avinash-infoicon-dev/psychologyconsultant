<?php

namespace App\Listeners;

use App\Events\SubscriptionCreated;
use App\Notifications\SubscriptionNotification;
use App\Services\NotificationService;

class UpdateQuota
{
    public function __construct(protected NotificationService $notifications) {}

    public function handle(SubscriptionCreated $event): void
    {
        $subscription = $event->subscription;
        $subscription->loadMissing(['patient.user', 'plan']);
        $patientUser = $subscription->patient?->user;

        if (! $patientUser) {
            return;
        }

        // Quota fields (exchanges_remaining/exchanges_used) are already
        // set correctly at creation time by SubscriptionService — this
        // listener's job is the resulting user-facing notification.
        $payload = SubscriptionNotification::for($subscription, 'created');

        $this->notifications->sendPayload($patientUser, $payload);
    }
}
