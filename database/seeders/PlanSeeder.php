<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

/**
 * §6.3 pricing grid (CHF, indicative).
 * Economic basis §6.1: CHF 190/h in-person, ≈12 min per exchange → hybrid model §6.2.
 */
class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Legacy tiers (pre-§6.3) — kept for reference, no longer seeded as active.
        // $legacyPlans = [
        //     [
        //         'name' => 'Starter',
        //         'slug' => 'starter',
        //         'description' => '4 message exchanges per month with a licensed psychologist.',
        //         'price' => 4900,
        //         'currency' => 'CHF',
        //         'billing_interval' => 'monthly',
        //         'exchange_quota' => 4,
        //         'features' => ['text_messaging', 'audio_messaging'],
        //         'is_active' => true,
        //         'sort_order' => 1,
        //     ],
        //     [
        //         'name' => 'Standard',
        //         'slug' => 'standard',
        //         'description' => '12 message exchanges per month, priority response time.',
        //         'price' => 9900,
        //         'currency' => 'CHF',
        //         'billing_interval' => 'monthly',
        //         'exchange_quota' => 12,
        //         'features' => ['text_messaging', 'audio_messaging', 'priority_response'],
        //         'is_active' => true,
        //         'sort_order' => 2,
        //     ],
        //     [
        //         'name' => 'Unlimited',
        //         'slug' => 'unlimited',
        //         'description' => 'Unlimited message exchanges, priority response time.',
        //         'price' => 19900,
        //         'currency' => 'CHF',
        //         'billing_interval' => 'monthly',
        //         'exchange_quota' => 999999,
        //         'features' => ['text_messaging', 'audio_messaging', 'priority_response', 'clinical_note_access'],
        //         'is_active' => true,
        //         'sort_order' => 3,
        //     ],
        // ];

        $plans = [
            [
                'name' => 'On-demand',
                'slug' => 'on-demand',
                'plan_type' => Plan::TYPE_ON_DEMAND,
                'description' => 'Single exchange for non-subscribers who want to try. Text + dictation. Response ≤ 48 working hours.',
                'price' => 4900, // CHF 49.– / exchange
                'currency' => 'CHF',
                'billing_interval' => 'per_exchange',
                'exchange_quota' => 1,
                'response_time_hours' => 48,
                'response_time_label' => '≤ 48 working hours',
                'channels' => [Plan::CHANNEL_TEXT, Plan::CHANNEL_DICTATION],
                'features' => [
                    '1 exchange (pay per use)',
                    'Text + dictation',
                    'Response ≤ 48 working hours',
                ],
                'is_active' => true,
                'status' => Plan::STATUS_ACTIVE,
                'is_recommended' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Essential',
                'slug' => 'essential',
                'plan_type' => Plan::TYPE_SUBSCRIPTION,
                'description' => 'Monthly subscription with 3 included exchanges. Text + dictation. Response ≤ 48 working hours.',
                'price' => 8900, // CHF 89.– / month
                'currency' => 'CHF',
                'billing_interval' => 'monthly',
                'exchange_quota' => 3,
                'response_time_hours' => 48,
                'response_time_label' => '≤ 48 working hours',
                'channels' => [Plan::CHANNEL_TEXT, Plan::CHANNEL_DICTATION],
                'features' => [
                    '3 exchanges / month',
                    'Text + dictation',
                    'Response ≤ 48 working hours',
                ],
                'is_active' => true,
                'status' => Plan::STATUS_ACTIVE,
                'is_recommended' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'plan_type' => Plan::TYPE_SUBSCRIPTION,
                'description' => 'Recommended monthly plan with 6 exchanges, faster response, and audio messages.',
                'price' => 16900, // CHF 169.– / month
                'currency' => 'CHF',
                'billing_interval' => 'monthly',
                'exchange_quota' => 6,
                'response_time_hours' => 24,
                'response_time_label' => '≤ 24 working hours',
                'channels' => [Plan::CHANNEL_TEXT, Plan::CHANNEL_DICTATION, Plan::CHANNEL_AUDIO],
                'features' => [
                    '6 exchanges / month',
                    'Text + dictation + audio',
                    'Response ≤ 24 working hours',
                    'Recommended',
                ],
                'is_active' => true,
                'status' => Plan::STATUS_ACTIVE,
                'is_recommended' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Intensive',
                'slug' => 'intensive',
                'plan_type' => Plan::TYPE_SUBSCRIPTION,
                'description' => 'Higher monthly volume (10 exchanges, fair use) with priority response under 24 hours and audio.',
                'price' => 27900, // CHF 279.– / month
                'currency' => 'CHF',
                'billing_interval' => 'monthly',
                'exchange_quota' => 10,
                'response_time_hours' => 24,
                'response_time_label' => 'priority < 24 hours',
                'channels' => [Plan::CHANNEL_TEXT, Plan::CHANNEL_DICTATION, Plan::CHANNEL_AUDIO],
                'features' => [
                    '10 exchanges / month (fair use)',
                    'Text + dictation + audio',
                    'Priority response < 24 hours',
                ],
                'is_active' => true,
                'status' => Plan::STATUS_ACTIVE,
                'is_recommended' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Additional exchange',
                'slug' => 'additional-exchange',
                'plan_type' => Plan::TYPE_ADDON,
                'description' => 'Extra exchange beyond the monthly quota. Response time follows the patient’s active plan.',
                'price' => 3900, // CHF 39.– / exchange
                'currency' => 'CHF',
                'billing_interval' => 'per_exchange',
                'exchange_quota' => 1,
                'response_time_hours' => null,
                'response_time_label' => 'according to plan',
                'channels' => null, // inherits from active subscription plan at purchase time
                'features' => [
                    '1 additional exchange',
                    'Response time according to plan',
                    'For quota overflow',
                ],
                'is_active' => true,
                'status' => Plan::STATUS_ACTIVE,
                'is_recommended' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        // Retire pre-§6.3 catalogue entries if they still exist.
        // Plan::query()
        //     ->whereIn('slug', ['starter', 'unlimited'])
        //     ->update(['is_active' => false, 'is_recommended' => false]);
        Plan::query()
            ->whereIn('slug', ['starter', 'unlimited'])
            ->update(['is_active' => false, 'is_recommended' => false, 'status' => Plan::STATUS_ARCHIVED]);
    }
}
