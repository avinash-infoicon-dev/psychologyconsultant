<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'plan_type' => Plan::TYPE_SUBSCRIPTION,
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(2900, 19900),
            'currency' => 'CHF',
            'billing_interval' => 'monthly',
            'exchange_quota' => fake()->numberBetween(3, 12),
            'response_time_hours' => 48,
            'response_time_label' => '≤ 48 working hours',
            'channels' => [Plan::CHANNEL_TEXT, Plan::CHANNEL_DICTATION],
            'features' => ['text_messaging', 'audio_messaging'],
            'is_active' => true,
            'status' => Plan::STATUS_ACTIVE,
            'is_recommended' => false,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
