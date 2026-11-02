<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\Payment\SubscriptionService;
use Illuminate\Console\Command;

class ResetQuotas extends Command
{
    protected $signature = 'healthcare:reset-quotas';

    protected $description = 'Renew exchange quotas for active Twint subscriptions whose billing period has ended (Stripe subscriptions renew via webhook).';

    public function handle(SubscriptionService $subscriptions): int
    {
        $due = Subscription::where('status', 'active')
            ->whereNull('stripe_subscription_id') // Twint-billed only
            ->where('current_period_ends_at', '<=', now())
            ->get();

        $this->info("Renewing {$due->count()} subscription(s)...");

        foreach ($due as $subscription) {
            $subscriptions->renew($subscription);
        }

        return self::SUCCESS;
    }
}
