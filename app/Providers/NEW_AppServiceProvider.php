<?php

namespace App\Providers;

use App\Contracts\SpeechDriverInterface;
use App\Models\ClinicalNote;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Psychologist;
use App\Policies\ClinicalNotePolicy;
use App\Policies\ConversationPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PatientPolicy;
use App\Policies\PsychologistPolicy;
use App\Services\Speech\NativeSpeechService;
use App\Services\Speech\WhisperService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * NOTE: if your project already has an AppServiceProvider, merge the
     * contents of boot() below into it rather than overwriting the file —
     * Laravel's default one may already contain unrelated app bootstrapping.
     */
    public function register(): void
    {
        $this->app->bind(SpeechDriverInterface::class, function () {
            return config('speech.driver') === 'native'
                ? $this->app->make(NativeSpeechService::class)
                : $this->app->make(WhisperService::class);
        });
    }

    public function boot(): void
    {
        // Admins can do anything. This must come before individual policy
        // checks are relied upon elsewhere, since Gate::before short-circuits
        // all ability checks (including ones on policies without an explicit
        // admin branch, like ConversationPolicy).
        Gate::before(function ($user, string $ability) {
            return $user->isAdmin() ? true : null;
        });

        Gate::policy(Conversation::class, ConversationPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(Psychologist::class, PsychologistPolicy::class);
        Gate::policy(ClinicalNote::class, ClinicalNotePolicy::class);

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\MessageSent::class,
            [\App\Listeners\NotifyPsychologist::class, 'handle']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\MessageSent::class,
            [\App\Listeners\NotifyPatient::class, 'handle']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\AudioUploaded::class,
            [\App\Listeners\DispatchTranscription::class, 'handle']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\PaymentCompleted::class,
            [\App\Listeners\CreateInvoice::class, 'handle']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\SubscriptionCreated::class,
            [\App\Listeners\UpdateQuota::class, 'handle']
        );

        // ClinicalNote and User use the Auditable trait directly (see
        // their own model files). Everything else listed in
        // config('security.audit.log_models') gets audit logging via
        // Observer instead, so those models don't need to be touched —
        // both mechanisms share the same App\Support\AuditLogger.
        $traitBased = ['ClinicalNote'];

        foreach (config('security.audit.log_models', []) as $modelName) {
            if (in_array($modelName, $traitBased, true)) {
                continue;
            }

            $class = "App\\Models\\{$modelName}";

            if (class_exists($class)) {
                $class::observe(\App\Observers\AuditObserver::class);
            }
        }
    }
}
