<?php

namespace Tests\Unit;

use App\Models\Plan;
use App\Models\Setting;
use App\Support\MessagingLimits;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagingLimitsMessagesPerExchangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'messaging.messages_per_exchange.patient' => 5,
            'messaging.messages_per_exchange.psychologist' => 5,
        ]);
    }

    public function test_plan_patient_override(): void
    {
        Setting::set('messaging.patient.messages_per_exchange', 5, 'integer', 'messaging');
        $plan = $this->plan(['patient_messages_per_exchange' => 10]);

        $this->assertSame(10, MessagingLimits::patientMessagesPerExchange($plan));
        $this->assertSame(MessagingLimits::SOURCE_PLAN, MessagingLimits::messagesPerExchangeSource($plan, 'patient'));
    }

    public function test_plan_psychologist_override(): void
    {
        Setting::set('messaging.psychologist.messages_per_exchange', 5, 'integer', 'messaging');
        $plan = $this->plan(['psychologist_messages_per_exchange' => 8]);

        $this->assertSame(8, MessagingLimits::psychologistMessagesPerExchange($plan));
    }

    public function test_null_plan_inherits_global(): void
    {
        Setting::set('messaging.patient.messages_per_exchange', 7, 'integer', 'messaging');
        $plan = $this->plan(['patient_messages_per_exchange' => null]);

        $this->assertSame(7, MessagingLimits::patientMessagesPerExchange($plan));
        $this->assertSame(MessagingLimits::SOURCE_GLOBAL, MessagingLimits::messagesPerExchangeSource($plan, 'patient'));
    }

    public function test_null_plan_and_global_uses_default(): void
    {
        $plan = $this->plan(['patient_messages_per_exchange' => null]);

        $this->assertSame(5, MessagingLimits::patientMessagesPerExchange($plan));
        $this->assertSame(MessagingLimits::SOURCE_DEFAULT, MessagingLimits::messagesPerExchangeSource($plan, 'patient'));
    }

    public function test_plan_only_when_global_missing(): void
    {
        $plan = $this->plan(['patient_messages_per_exchange' => 10]);

        $this->assertSame(10, MessagingLimits::patientMessagesPerExchange($plan));
    }

    public function test_plan_patient_zero_psychologist_inherits_global(): void
    {
        Setting::set('messaging.patient.messages_per_exchange', 5, 'integer', 'messaging');
        Setting::set('messaging.psychologist.messages_per_exchange', 5, 'integer', 'messaging');
        $plan = $this->plan([
            'patient_messages_per_exchange' => 0,
            'psychologist_messages_per_exchange' => null,
        ]);

        $this->assertNull(MessagingLimits::patientMessagesPerExchange($plan));
        $this->assertSame(5, MessagingLimits::psychologistMessagesPerExchange($plan));
        $this->assertSame(0, MessagingLimits::snapshotMessagesPerExchange($plan, MessagingLimits::ROLE_PATIENT));
        $this->assertSame(5, MessagingLimits::snapshotMessagesPerExchange($plan, MessagingLimits::ROLE_PSYCHOLOGIST));
    }

    public function test_config_zero_is_unlimited(): void
    {
        config(['messaging.messages_per_exchange.patient' => 0]);
        $plan = $this->plan(['patient_messages_per_exchange' => null]);

        $this->assertNull(MessagingLimits::patientMessagesPerExchange($plan));
        $this->assertSame(0, MessagingLimits::snapshotMessagesPerExchange($plan, 'patient'));
        $this->assertSame(MessagingLimits::SOURCE_DEFAULT, MessagingLimits::messagesPerExchangeSource($plan, 'patient'));
    }

    public function test_independent_patient_and_psychologist(): void
    {
        Setting::set('messaging.patient.messages_per_exchange', 5, 'integer', 'messaging');
        Setting::set('messaging.psychologist.messages_per_exchange', 7, 'integer', 'messaging');
        $plan = $this->plan([
            'patient_messages_per_exchange' => 10,
            'psychologist_messages_per_exchange' => null,
        ]);

        $this->assertSame(10, MessagingLimits::patientMessagesPerExchange($plan));
        $this->assertSame(7, MessagingLimits::psychologistMessagesPerExchange($plan));
    }

    public function test_plan_zero_is_unlimited_and_does_not_inherit(): void
    {
        Setting::set('messaging.patient.messages_per_exchange', 5, 'integer', 'messaging');
        $plan = $this->plan(['patient_messages_per_exchange' => 0]);

        $this->assertNull(MessagingLimits::patientMessagesPerExchange($plan));
        $this->assertSame(0, MessagingLimits::snapshotMessagesPerExchange($plan, 'patient'));
        $this->assertSame(MessagingLimits::SOURCE_PLAN, MessagingLimits::messagesPerExchangeSource($plan, 'patient'));
    }

    public function test_global_zero_is_unlimited(): void
    {
        Setting::set('messaging.patient.messages_per_exchange', 0, 'integer', 'messaging');
        $plan = $this->plan(['patient_messages_per_exchange' => null]);

        $this->assertNull(MessagingLimits::patientMessagesPerExchange($plan));
        $this->assertSame(MessagingLimits::SOURCE_GLOBAL, MessagingLimits::messagesPerExchangeSource($plan, 'patient'));
    }

    public function test_empty_plan_form_stays_null(): void
    {
        $plan = $this->plan(['patient_messages_per_exchange' => null]);
        $this->assertNull($plan->getRawOriginal('patient_messages_per_exchange'));
    }

    protected function plan(array $overrides = []): Plan
    {
        return Plan::query()->create(array_merge([
            'name' => 'Test plan',
            'slug' => 'test-plan-'.uniqid(),
            'plan_type' => Plan::TYPE_SUBSCRIPTION,
            'price' => 1000,
            'currency' => 'CHF',
            'billing_interval' => 'monthly',
            'exchange_quota' => 10,
            'status' => Plan::STATUS_ACTIVE,
            'is_active' => true,
        ], $overrides));
    }
}
