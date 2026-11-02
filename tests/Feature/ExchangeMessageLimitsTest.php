<?php

namespace Tests\Feature;

use App\Exceptions\ExchangeMessageLimitException;
use App\Exceptions\PsychologistUnavailableException;
use App\Models\Conversation;
use App\Models\Exchange;
use App\Models\ExchangeTransaction;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\ProfessionalType;
use App\Models\Psychologist;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Messaging\MessageService;
use App\Support\MessagingLimits;
use App\Support\PsychologistCareStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ExchangeMessageLimitsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        config([
            'messaging.messages_per_exchange.patient' => 5,
            'messaging.messages_per_exchange.psychologist' => 5,
        ]);
        $this->ensureRoles();
    }

    public function test_new_exchange_snapshots_limits_and_counters_start_at_zero(): void
    {
        $ctx = $this->messagingContext(patientLimit: 10, psychLimit: 4);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'hello');

        $exchange = Exchange::query()->first();
        $this->assertNotNull($exchange);
        $this->assertSame(10, (int) $exchange->patient_message_limit);
        $this->assertSame(4, (int) $exchange->psychologist_message_limit);
        $this->assertSame(1, (int) $exchange->patient_messages_used);
        $this->assertSame(0, (int) $exchange->psychologist_messages_used);
    }

    public function test_changing_plan_does_not_alter_existing_snapshot(): void
    {
        $ctx = $this->messagingContext(patientLimit: 3, psychLimit: 3);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'one');
        $ctx['plan']->update(['patient_messages_per_exchange' => 99]);

        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'two');
        $exchange = Exchange::query()->first();
        $this->assertSame(3, (int) $exchange->patient_message_limit);
        $this->assertSame(2, (int) $exchange->patient_messages_used);
    }

    public function test_lazy_init_only_fills_missing_side(): void
    {
        $ctx = $this->messagingContext(patientLimit: 5, psychLimit: 6);
        $message = $ctx['conversation']->messages()->create([
            'sender_id' => $ctx['patientUser']->id,
            'type' => 'text',
            'body' => 'seed',
        ]);
        $exchange = Exchange::query()->create([
            'conversation_id' => $ctx['conversation']->id,
            'patient_id' => $ctx['patient']->id,
            'psychologist_id' => $ctx['psychologist']->id,
            'subscription_id' => $ctx['subscription']->id,
            'patient_message_id' => $message->id,
            'status' => Exchange::STATUS_PENDING,
            'patient_message_limit' => 5,
            'psychologist_message_limit' => null,
            'patient_messages_used' => 1,
            'psychologist_messages_used' => 0,
        ]);

        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'follow-up');
        $exchange->refresh();
        $this->assertSame(5, (int) $exchange->patient_message_limit);
        $this->assertSame(6, (int) $exchange->psychologist_message_limit);
    }

    public function test_patient_is_blocked_at_limit(): void
    {
        $ctx = $this->messagingContext(patientLimit: 2, psychLimit: 5);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], '1');
        $this->sendText($ctx['patientUser'], $ctx['conversation'], '2');

        $this->expectException(ExchangeMessageLimitException::class);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], '3');
    }

    public function test_unlimited_patient_is_not_blocked(): void
    {
        $ctx = $this->messagingContext(patientLimit: 0, psychLimit: 5);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], '1');
        $this->sendText($ctx['patientUser'], $ctx['conversation'], '2');
        $this->sendText($ctx['patientUser'], $ctx['conversation'], '3');

        $exchange = Exchange::query()->first();
        $this->assertSame(0, (int) $exchange->patient_message_limit);
        $this->assertSame(3, (int) $exchange->patient_messages_used);
        $this->assertNull(
            app(\App\Services\Messaging\ExchangeQuotaService::class)
                ->currentExchangeMessageUsage($ctx['conversation'], $ctx['patientUser'])
        );
    }

    public function test_psychologist_follow_up_does_not_consume_another_exchange(): void
    {
        $ctx = $this->messagingContext(patientLimit: 5, psychLimit: 5);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'request');
        $this->sendText($ctx['psychUser'], $ctx['conversation'], 'reply 1');
        $this->sendText($ctx['psychUser'], $ctx['conversation'], 'reply 2');

        $ctx['subscription']->refresh();
        $this->assertSame(1, (int) $ctx['subscription']->consumed_exchanges);
        $this->assertSame(1, Exchange::query()->where('status', Exchange::STATUS_COMPLETED)->count());
        $this->assertSame(1, ExchangeTransaction::query()->where('type', ExchangeTransaction::TYPE_CONSUMED)->count());

        $exchange = Exchange::query()->first();
        $this->assertSame(2, (int) $exchange->psychologist_messages_used);
    }

    public function test_global_zero_hides_counter_and_unblocks_existing_snapshot(): void
    {
        $ctx = $this->messagingContext(patientLimit: 5, psychLimit: 5);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'request');
        foreach (range(1, 5) as $i) {
            $this->sendText($ctx['psychUser'], $ctx['conversation'], 'r'.$i);
        }

        $quota = app(\App\Services\Messaging\ExchangeQuotaService::class);
        $this->assertTrue(
            (bool) ($quota->currentExchangeMessageUsage($ctx['conversation'], $ctx['psychUser'])['at_cap'] ?? false)
        );

        $ctx['plan']->update([
            'patient_messages_per_exchange' => null,
            'psychologist_messages_per_exchange' => null,
        ]);
        Setting::set('messaging.patient.messages_per_exchange', 0, 'integer', 'messaging');
        Setting::set('messaging.psychologist.messages_per_exchange', 0, 'integer', 'messaging');

        $this->assertNull(
            $quota->currentExchangeMessageUsage($ctx['conversation']->fresh(), $ctx['psychUser'])
        );

        $this->sendText($ctx['psychUser'], $ctx['conversation'], 'after unlimited');
        $this->assertSame(6, (int) Exchange::query()->first()->psychologist_messages_used);
    }

    public function test_psychologist_cannot_exceed_message_limit(): void
    {
        $ctx = $this->messagingContext(patientLimit: 5, psychLimit: 1);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'request');
        $this->sendText($ctx['psychUser'], $ctx['conversation'], 'reply');

        $this->expectException(ExchangeMessageLimitException::class);
        $this->sendText($ctx['psychUser'], $ctx['conversation'], 'too many');
    }

    public function test_five_plus_five_messages_still_one_consumed_exchange(): void
    {
        $ctx = $this->messagingContext(patientLimit: 5, psychLimit: 5);
        foreach (range(1, 5) as $i) {
            $this->sendText($ctx['patientUser'], $ctx['conversation'], 'p'.$i);
        }
        foreach (range(1, 5) as $i) {
            $this->sendText($ctx['psychUser'], $ctx['conversation'], 'r'.$i);
        }

        $ctx['subscription']->refresh();
        $this->assertSame(1, (int) $ctx['subscription']->consumed_exchanges);
        $exchange = Exchange::query()->first();
        $this->assertSame(5, (int) $exchange->patient_messages_used);
        $this->assertSame(5, (int) $exchange->psychologist_messages_used);
    }

    public function test_sequential_sends_cannot_exceed_limit(): void
    {
        $ctx = $this->messagingContext(patientLimit: 1, psychLimit: 5);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'only');

        try {
            $this->sendText($ctx['patientUser'], $ctx['conversation'], 'extra');
            $this->fail('Expected ExchangeMessageLimitException');
        } catch (ExchangeMessageLimitException) {
            $this->assertSame(1, (int) Exchange::query()->first()->patient_messages_used);
        }
    }

    public function test_plan_validation_empty_zero_and_negative(): void
    {
        $rules = [
            'patient_messages_per_exchange' => ['nullable', 'integer', 'min:0'],
            'psychologist_messages_per_exchange' => ['nullable', 'integer', 'min:0'],
        ];

        $this->assertTrue(validator(['patient_messages_per_exchange' => null], $rules)->passes());
        $this->assertTrue(validator(['patient_messages_per_exchange' => ''], $rules)->passes());
        $this->assertTrue(validator(['patient_messages_per_exchange' => 0], $rules)->passes());
        $this->assertTrue(validator(['patient_messages_per_exchange' => 8], $rules)->passes());
        $this->assertTrue(validator(['patient_messages_per_exchange' => -1], $rules)->fails());
        $this->assertTrue(validator(['psychologist_messages_per_exchange' => -2], $rules)->fails());

        $controller = app(\App\Http\Controllers\Backend\PlanController::class);
        $method = new \ReflectionMethod($controller, 'nullableInt');
        $this->assertNull($method->invoke($controller, null));
        $this->assertNull($method->invoke($controller, ''));
        $this->assertSame(0, $method->invoke($controller, 0));
        $this->assertSame(0, $method->invoke($controller, '0'));
        $this->assertSame(4, $method->invoke($controller, '4'));
    }

    public function test_global_settings_accept_zero_and_reject_negative(): void
    {
        $rules = [
            'patient_messages_per_exchange' => ['required', 'integer', 'min:0'],
            'psychologist_messages_per_exchange' => ['required', 'integer', 'min:0'],
        ];

        $this->assertTrue(validator([
            'patient_messages_per_exchange' => 0,
            'psychologist_messages_per_exchange' => 0,
        ], $rules)->passes());
        $this->assertTrue(validator([
            'patient_messages_per_exchange' => -1,
            'psychologist_messages_per_exchange' => 5,
        ], $rules)->fails());

        Setting::set('messaging.patient.messages_per_exchange', 0, 'integer', 'messaging');
        $plan = Plan::query()->create([
            'name' => 'Inherit zero',
            'slug' => 'inherit-zero-'.uniqid(),
            'plan_type' => Plan::TYPE_SUBSCRIPTION,
            'price' => 1000,
            'currency' => 'CHF',
            'billing_interval' => 'monthly',
            'exchange_quota' => 10,
            'status' => Plan::STATUS_ACTIVE,
            'is_active' => true,
            'patient_messages_per_exchange' => null,
        ]);

        $this->assertNull(MessagingLimits::patientMessagesPerExchange($plan));
    }

    public function test_atomic_increment_cannot_push_used_above_limit(): void
    {
        $ctx = $this->messagingContext(patientLimit: 1, psychLimit: 5);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'only');
        $exchange = Exchange::query()->first();

        $updated = Exchange::query()
            ->whereKey($exchange->id)
            ->where('patient_messages_used', '<', 1)
            ->increment('patient_messages_used');

        $this->assertSame(0, $updated);
        $this->assertSame(1, (int) $exchange->fresh()->patient_messages_used);
        $this->assertSame(1, $ctx['conversation']->messages()->count());
    }

    public function test_inactive_psychologist_blocks_new_messages_but_keeps_history(): void
    {
        $ctx = $this->messagingContext(patientLimit: 5, psychLimit: 5);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'hello');

        $ctx['psychUser']->update(['is_active' => false]);
        $ctx['psychologist']->unsetRelation('user');

        $this->assertSame(
            PsychologistCareStatus::INACTIVE,
            PsychologistCareStatus::for($ctx['psychologist']->fresh(['user']))
        );
        $this->assertSame(1, $ctx['conversation']->messages()->count());

        $this->expectException(PsychologistUnavailableException::class);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'should fail');
    }

    public function test_suspended_psychologist_blocks_new_messages(): void
    {
        $ctx = $this->messagingContext(patientLimit: 5, psychLimit: 5);
        $this->sendText($ctx['patientUser'], $ctx['conversation'], 'hello');

        $ctx['psychologist']->update([
            'verification_status' => Psychologist::VERIFICATION_SUSPENDED,
        ]);

        $this->assertSame(
            PsychologistCareStatus::SUSPENDED,
            PsychologistCareStatus::for($ctx['psychologist']->fresh(['user']))
        );

        $this->expectException(PsychologistUnavailableException::class);
        $this->sendText($ctx['psychUser'], $ctx['conversation'], 'should fail');
    }

    public function test_plan_zero_rejected_is_not_required_zero_is_unlimited(): void
    {
        $plan = Plan::query()->create([
            'name' => 'Zero plan',
            'slug' => 'zero-plan',
            'plan_type' => Plan::TYPE_SUBSCRIPTION,
            'price' => 1000,
            'currency' => 'CHF',
            'billing_interval' => 'monthly',
            'exchange_quota' => 10,
            'status' => Plan::STATUS_ACTIVE,
            'is_active' => true,
            'patient_messages_per_exchange' => 0,
        ]);

        $this->assertSame(0, (int) $plan->getRawOriginal('patient_messages_per_exchange'));
        $this->assertNull(MessagingLimits::patientMessagesPerExchange($plan));
    }

    /**
     * @return array{
     *     patientUser: User,
     *     psychUser: User,
     *     patient: Patient,
     *     psychologist: Psychologist,
     *     conversation: Conversation,
     *     plan: Plan,
     *     subscription: Subscription
     * }
     */
    protected function messagingContext(int $patientLimit, int $psychLimit): array
    {
        $plan = Plan::query()->create([
            'name' => 'Msg plan',
            'slug' => 'msg-plan-'.uniqid(),
            'plan_type' => Plan::TYPE_SUBSCRIPTION,
            'price' => 1000,
            'currency' => 'CHF',
            'billing_interval' => 'monthly',
            'exchange_quota' => 20,
            'status' => Plan::STATUS_ACTIVE,
            'is_active' => true,
            'patient_messages_per_exchange' => $patientLimit,
            'psychologist_messages_per_exchange' => $psychLimit,
            'channels' => [Plan::CHANNEL_TEXT, Plan::CHANNEL_DICTATION, Plan::CHANNEL_AUDIO],
        ]);

        $patientUser = User::factory()->create();
        $patientUser->assignRbacRole('patient');
        $psychUser = User::factory()->create();
        $psychUser->assignRbacRole('psychologist');

        $type = ProfessionalType::query()->create([
            'name' => 'Psychologist',
            'slug' => 'psychologist-'.uniqid(),
            'status' => true,
        ]);

        $psychologist = Psychologist::query()->create([
            'user_id' => $psychUser->id,
            'professional_type_id' => $type->id,
            'first_name' => 'Pat',
            'last_name' => 'Smith',
            'slug' => 'pat-smith-'.uniqid(),
            'is_verified' => true,
            'verification_status' => Psychologist::VERIFICATION_VERIFIED,
            'is_accepting_patients' => true,
            'status' => true,
        ]);

        $patient = Patient::query()->create([
            'user_id' => $patientUser->id,
            'psychologist_id' => $psychologist->id,
        ]);

        $subscription = Subscription::query()->create([
            'patient_id' => $patient->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'current_period_starts_at' => now(),
            'current_period_ends_at' => now()->addMonth(),
            'included_exchanges' => 20,
            'consumed_exchanges' => 0,
            'purchased_exchanges' => 0,
            'exchanges_used' => 0,
            'exchanges_remaining' => 20,
        ]);

        $conversation = Conversation::query()->create([
            'patient_id' => $patient->id,
            'psychologist_id' => $psychologist->id,
            'status' => 'active',
        ]);

        $patientUser->unsetRelation('roles');
        $psychUser->unsetRelation('roles');
        $patientUser->load('roles');
        $psychUser->load('roles');
        $patientUser->setRelation('patient', $patient);
        $psychUser->setRelation('professional', $psychologist);

        return compact('patientUser', 'psychUser', 'patient', 'psychologist', 'conversation', 'plan', 'subscription');
    }

    protected function sendText(User $sender, Conversation $conversation, string $body): void
    {
        app(MessageService::class)->send($sender, $conversation, $body);
    }

    protected function ensureRoles(): void
    {
        foreach (['patient', 'psychologist', 'admin'] as $slug) {
            Role::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => ucfirst($slug), 'is_active' => true]
            );
        }
    }
}
