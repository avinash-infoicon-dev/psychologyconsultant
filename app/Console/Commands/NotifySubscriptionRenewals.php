<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class NotifySubscriptionRenewals extends Command
{
    protected $signature = 'healthcare:notify-renewals';

    protected $description = 'Notify patients whose subscription renews within the configured reminder window.';

    public function handle(NotificationService $notifications): int
    {
        $days = (int) config('notification.renewal_reminder_days', 7);
        $from = now()->startOfDay();
        $until = now()->addDays($days)->endOfDay();

        Subscription::query()
            ->with(['patient.user', 'plan'])
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIALING])
            ->whereBetween('current_period_ends_at', [$from, $until])
            ->chunkById(200, function ($subscriptions) use ($notifications) {
                foreach ($subscriptions as $subscription) {
                    $user = $subscription->patient?->user;
                    if (! $user) {
                        continue;
                    }

                    $periodKey = $subscription->current_period_ends_at?->toDateString() ?: $subscription->id;
                    $payload = SubscriptionNotification::for($subscription, 'renewal_approaching');
                    $notifications->sendPayload(
                        $user,
                        $payload,
                        'renewal:'.$subscription->id.':'.$periodKey,
                    );
                }
            });

        return self::SUCCESS;
    }
}
