<?php

namespace Tests\Feature;

use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Events\SubscriptionCreated;
use App\Mail\GenericNotificationMail;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_the_reset_link_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('frontend.auth.password.email'), ['email' => $user->email])
            ->assertSessionHas('success');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_successful_web_password_reset_sends_a_locked_security_alert(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->post(route('frontend.auth.password.update'), [
            'email' => $user->email,
            'token' => $token,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect(route('frontend.auth.login'));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'security_alert',
        ]);
        Mail::assertQueued(GenericNotificationMail::class);
    }

    public function test_successful_api_password_reset_sends_a_locked_security_alert(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->postJson('/api/v1/auth/password/reset', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'security_alert',
        ]);
        Mail::assertQueued(GenericNotificationMail::class);
    }

    public function test_new_subscription_creates_the_in_app_notification_without_a_queue_worker(): void
    {
        Mail::fake();
        Queue::fake();

        $user = User::factory()->create();
        $patient = Patient::query()->create(['user_id' => $user->id]);
        $plan = Plan::factory()->create();
        $subscription = Subscription::query()->create([
            'patient_id' => $patient->id,
            'plan_id' => $plan->id,
            'plan_snapshot' => $plan->toSnapshot(),
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'current_period_starts_at' => now(),
            'current_period_ends_at' => now()->addMonth(),
            'included_exchanges' => $plan->exchange_quota,
            'consumed_exchanges' => 0,
            'purchased_exchanges' => 0,
            'exchanges_used' => 0,
            'exchanges_remaining' => $plan->exchange_quota,
            'auto_renew' => true,
            'cancel_at_period_end' => false,
        ]);

        event(new SubscriptionCreated($subscription));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'payment',
        ]);
        Mail::assertSent(GenericNotificationMail::class);
        Queue::assertNothingPushed();
    }

    public function test_successful_payment_notifies_the_payer_without_a_direct_subscription_relation(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'amount' => 1500,
            'currency' => 'CHF',
            'gateway' => 'stripe',
            'status' => 'succeeded',
        ]);

        event(new PaymentCompleted($payment));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'payment',
            'dedupe_key' => 'payment:'.$payment->id.':succeeded',
        ]);
        Mail::assertSent(GenericNotificationMail::class);
    }

    public function test_failed_payment_notifies_the_payer_once(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'amount' => 1500,
            'currency' => 'CHF',
            'gateway' => 'stripe',
            'status' => 'failed',
        ]);

        event(new PaymentFailed($payment));
        event(new PaymentFailed($payment));

        $this->assertSame(
            1,
            $user->notifications()->where('dedupe_key', 'payment:'.$payment->id.':failed')->count(),
        );
        Mail::assertSent(GenericNotificationMail::class);
    }
}
