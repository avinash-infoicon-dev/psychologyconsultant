<?php

namespace App\Providers;

use App\Models\ClinicalNote;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Psychologist;
use App\Models\Report;
use App\Models\User;
use App\Policies\ClinicalNotePolicy;
use App\Policies\ConversationPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PatientPolicy;
use App\Policies\PsychologistPolicy;
use App\Policies\ReportPolicy;
use Dedoc\Scramble\Scramble;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

use App\Contracts\SpeechDriverInterface;
use App\Events\AudioUploaded;
use App\Events\MessageSent;
use App\Listeners\DispatchTranscription;
use App\Listeners\NotifyPatient;
use App\Listeners\NotifyPsychologist;
use App\Observers\AuditObserver;
use App\Services\Speech\NativeSpeechService;
use App\Services\Speech\WhisperService;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
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
        Paginator::useBootstrapFive();

        // Match generated asset/storage URLs to the browser request. Demo is often
        // opened as https while APP_URL is http, which hides CSS background images.
        if (! $this->app->runningInConsole()) {
            $root = rtrim((string) request()->root(), '/');
            if ($root !== '') {
                URL::forceRootUrl($root);
                config(['filesystems.disks.public.url' => $root.'/storage']);
            }
            if (request()->isSecure()) {
                URL::forceScheme('https');
            }
        }

        Gate::policy(Conversation::class, ConversationPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(Psychologist::class, PsychologistPolicy::class);
        Gate::policy(ClinicalNote::class, ClinicalNotePolicy::class);
        Gate::policy(Report::class, ReportPolicy::class);

        Gate::before(function (User $user, string $ability) {
            if ($user->hasFullAccess()) {
                return true;
            }

            // Permission slugs use dots (e.g. users.view). Policy methods do not (e.g. view, update).
            if (str_contains($ability, '.')) {
                return $user->hasPermissionTo($ability) ?: null;
            }

            return null;
        });

        // Scramble API docs: admin / superadmin only (all environments)
        Gate::define('viewApiDocs', function (User $user) {
            return $user->is_active && $user->hasAnyRbacRole(['superadmin', 'admin']);
        });

        // Document auth + patient API routes (must share api_path prefix in config/scramble.php).
        Scramble::configure()
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri(), ['api/v1/auth', 'api/v1/patient']);
            });

        Blade::if('role', function (string|array $roles) {
            return auth()->check() && auth()->user()->hasRbacRole($roles);
        });

        Blade::if('permission', function (string $permission) {
            return auth()->check() && auth()->user()->hasPermissionTo($permission);
        });

        // MessageSent / AudioUploaded listeners live in app/Listeners and are
        // auto-discovered. Registering them here as well queued every notification twice.
        // Event::listen(MessageSent::class, [NotifyPsychologist::class, 'handle']);
        // Event::listen(MessageSent::class, [NotifyPatient::class, 'handle']);
        // Event::listen(AudioUploaded::class, [DispatchTranscription::class, 'handle']);
        // \Illuminate\Support\Facades\Event::listen(
        //     \App\Events\PaymentCompleted::class,
        //     [\App\Listeners\CreateInvoice::class, 'handle']
        // );
        // \Illuminate\Support\Facades\Event::listen(
        //     \App\Events\SubscriptionCreated::class,
        //     [\App\Listeners\UpdateQuota::class, 'handle']
        // );


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
                $class::observe(AuditObserver::class);
            }
        }
    }
}
