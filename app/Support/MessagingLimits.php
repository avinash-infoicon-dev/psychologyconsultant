<?php

namespace App\Support;

use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;

/**
 * §5.5 exchange-unit limits (admin-configurable per role) combined with
 * the patient's active plan text/audio caps.
 *
 * A return value of null means unlimited (no counter in chat).
 *
 * Resolution (first positive value wins):
 * 1. Active subscription plan limit
 * 2. Global Messaging settings for the role (0 = skip this level)
 * 3. Default from config/messaging.php
 */
class MessagingLimits
{
    public const ROLE_PATIENT = 'patient';

    public const ROLE_PSYCHOLOGIST = 'psychologist';

    public const SOURCE_PLAN = 'plan';

    public const SOURCE_GLOBAL = 'global';

    public const SOURCE_DEFAULT = 'default';

    /**
     * Maximum text characters for the given user, or null if unlimited.
     */
    public static function maxTextChars(?User $user = null): ?int
    {
        // Old: plan replaced global whenever the plan field was set.
        // $fromPlan = self::limitFromActivePlan($user, 'max_text_chars');
        // if ($fromPlan !== false) {
        //     return $fromPlan;
        // }
        // return self::limitFor(...);

        // Old: min(plan, global); global 0 left the chat with no counter
        // when the plan also had no cap.
        // $fromPlan = self::limitFromActivePlan($user, 'max_text_chars');
        // $fromGlobal = self::limitFor(...);
        // return self::combinePlanAndGlobal($fromPlan, $fromGlobal);

        return self::resolveLimit(
            $user,
            'max_text_chars',
            'messaging.max_text_chars',
            (int) config('messaging.patient.max_text_chars', config('messaging.max_text_chars', 2000)),
        );
    }

    /**
     * Maximum audio seconds for the given user, or null if unlimited.
     */
    public static function maxAudioSeconds(?User $user = null): ?int
    {
        // Old: plan replaced global whenever the plan field was set.
        // $fromPlan = self::limitFromActivePlan($user, 'max_audio_seconds');
        // if ($fromPlan !== false) {
        //     return $fromPlan;
        // }
        // return self::limitFor(...);

        // Old: min(plan, global); global 0 hid the chat audio cap.
        // $fromPlan = self::limitFromActivePlan($user, 'max_audio_seconds');
        // $fromGlobal = self::limitFor(...);
        // return self::combinePlanAndGlobal($fromPlan, $fromGlobal);

        return self::resolveLimit(
            $user,
            'max_audio_seconds',
            'messaging.max_audio_seconds',
            (int) config('messaging.patient.max_audio_seconds', config('messaging.max_audio_seconds', 60)),
        );
    }

    /**
     * Raw admin value for a role (0 means unlimited). Used by admin forms.
     */
    public static function configuredLimit(string $role, string $field): int
    {
        $role = self::normalizeRole($role);
        $configDefault = (int) config("messaging.{$role}.{$field}", 0);

        $value = Setting::get("messaging.{$role}.{$field}");

        if ($value === null && $role === self::ROLE_PATIENT) {
            // Migrate from legacy global keys when role keys are not set yet.
            $legacyKey = $field === 'max_text_chars'
                ? 'messaging.max_text_chars'
                : 'messaging.max_audio_seconds';
            $legacy = Setting::get($legacyKey);
            if ($legacy !== null) {
                return max(0, (int) $legacy);
            }
        }

        if ($value === null) {
            return max(0, $configDefault);
        }

        return max(0, (int) $value);
    }

    /**
     * Whether a positive cap is active for this user+field.
     */
    public static function hasTextLimit(?User $user = null): bool
    {
        return self::maxTextChars($user) !== null;
    }

    public static function hasAudioLimit(?User $user = null): bool
    {
        return self::maxAudioSeconds($user) !== null;
    }

    /**
     * Catalog of chat audio formats the admin can enable.
     *
     * @return array<string, array{label: string, extensions: list<string>, mimes: list<string>}>
     */
    public static function audioFormatCatalog(): array
    {
        return [
            'webm' => [
                'label' => 'WebM',
                'extensions' => ['webm'],
                'mimes' => ['audio/webm', 'video/webm'],
            ],
            'ogg' => [
                'label' => 'Ogg',
                'extensions' => ['ogg', 'oga'],
                'mimes' => ['audio/ogg', 'video/ogg'],
            ],
            'mp3' => [
                'label' => 'MP3',
                'extensions' => ['mp3'],
                'mimes' => ['audio/mpeg'],
            ],
            'wav' => [
                'label' => 'WAV',
                'extensions' => ['wav'],
                'mimes' => ['audio/wav', 'audio/x-wav'],
            ],
            'm4a' => [
                'label' => 'M4A / MP4',
                'extensions' => ['m4a', 'mp4'],
                'mimes' => ['audio/mp4', 'audio/x-m4a'],
            ],
        ];
    }

    /**
     * Max voice-message upload size in KB (admin Messaging page, then config).
     */
    public static function maxAudioUploadKb(): int
    {
        $stored = Setting::get('messaging.audio.max_upload_size_kb');
        if ($stored !== null && $stored !== '') {
            $kb = (int) $stored;
            if ($kb > 0) {
                return $kb;
            }
        }

        // Legacy: config/messaging.php then config/speech.php
        $fromMessaging = (int) config('messaging.audio.max_upload_size_kb', 0);
        if ($fromMessaging > 0) {
            return $fromMessaging;
        }

        return max(1, (int) config('speech.max_upload_size_kb', 25600));
    }

    public static function maxAudioUploadMb(): int
    {
        return max(1, (int) ceil(self::maxAudioUploadKb() / 1024));
    }

    /**
     * Format keys currently enabled in admin (webm, ogg, mp3, wav, m4a).
     *
     * @return list<string>
     */
    public static function configuredAudioFormatKeys(): array
    {
        $catalogKeys = array_keys(self::audioFormatCatalog());
        $stored = Setting::get('messaging.audio.allowed_formats');

        if (is_array($stored) && $stored !== []) {
            $keys = array_values(array_intersect($catalogKeys, $stored));
            if ($keys !== []) {
                return $keys;
            }
        }

        $fromConfig = config('messaging.audio.allowed_formats', $catalogKeys);

        return array_values(array_intersect(
            $catalogKeys,
            is_array($fromConfig) ? $fromConfig : $catalogKeys
        )) ?: $catalogKeys;
    }

    /**
     * @param  list<string>  $keys
     * @return list<string>
     */
    public static function extensionsFromFormatKeys(array $keys): array
    {
        $catalog = self::audioFormatCatalog();
        $extensions = [];

        foreach ($keys as $key) {
            foreach ($catalog[$key]['extensions'] ?? [] as $extension) {
                $extensions[] = $extension;
            }
        }

        return array_values(array_unique($extensions));
    }

    /**
     * @return list<string>
     */
    public static function allowedAudioExtensions(): array
    {
        $extensions = self::extensionsFromFormatKeys(self::configuredAudioFormatKeys());

        return $extensions !== [] ? $extensions : ['webm', 'ogg', 'oga', 'mp3', 'mp4', 'm4a', 'wav'];
    }

    /**
     * MIME types accepted for the enabled formats (includes MediaRecorder containers).
     *
     * @return list<string>
     */
    public static function allowedAudioMimeTypes(): array
    {
        $catalog = self::audioFormatCatalog();
        $mimes = [];

        foreach (self::configuredAudioFormatKeys() as $key) {
            foreach ($catalog[$key]['mimes'] ?? [] as $mime) {
                $mimes[] = $mime;
            }
        }

        // MediaRecorder sometimes reports an empty/generic type.
        if (array_intersect(['webm', 'ogg'], self::configuredAudioFormatKeys()) !== []) {
            $mimes[] = 'application/octet-stream';
        }

        // Legacy speech mime list kept as a fallback if admin keys somehow empty.
        // $mimes = array_merge($mimes, config('speech.allowed_mime_types', []));

        return array_values(array_unique($mimes)) ?: array_merge(
            config('speech.allowed_mime_types', []),
            ['video/webm', 'video/ogg', 'audio/x-wav', 'audio/x-m4a', 'application/octet-stream'],
        );
    }

    public static function audioFileAllowed(UploadedFile $file): bool
    {
        $extension = strtolower((string) $file->getClientOriginalExtension());
        if ($extension !== '' && in_array($extension, self::allowedAudioExtensions(), true)) {
            return true;
        }

        $mime = strtolower((string) ($file->getMimeType() ?: ''));

        return $mime !== '' && in_array($mime, self::allowedAudioMimeTypes(), true);
    }

    /**
     * Messages the patient may send in one exchange.
     * null = unlimited. Uses the patient's plan (not the psychologist's).
     */
    public static function patientMessagesPerExchange(?Plan $plan): ?int
    {
        return self::resolveMessagesPerExchange($plan, 'patient_messages_per_exchange', self::ROLE_PATIENT);
    }

    /**
     * Messages the psychologist may send in one exchange (from the patient's plan).
     * null = unlimited.
     */
    public static function psychologistMessagesPerExchange(?Plan $plan): ?int
    {
        return self::resolveMessagesPerExchange($plan, 'psychologist_messages_per_exchange', self::ROLE_PSYCHOLOGIST);
    }

    /**
     * Raw admin form value (including 0 = unlimited).
     */
    public static function configuredMessagesPerExchange(string $role): int
    {
        $role = self::normalizeRole($role);
        $stored = Setting::get("messaging.{$role}.messages_per_exchange");

        if ($stored !== null && $stored !== '') {
            return max(0, (int) $stored);
        }

        return max(0, (int) config("messaging.messages_per_exchange.{$role}", 5));
    }

    /**
     * Which level produced the effective cap: plan, global, or default.
     */
    public static function messagesPerExchangeSource(?Plan $plan, string $role): string
    {
        $role = self::normalizeRole($role);
        $column = $role === self::ROLE_PSYCHOLOGIST
            ? 'psychologist_messages_per_exchange'
            : 'patient_messages_per_exchange';

        if ($plan) {
            $raw = $plan->getRawOriginal($column);
            if ($raw === null) {
                $raw = $plan->{$column};
            }
            if ($raw !== null && $raw !== '') {
                return self::SOURCE_PLAN;
            }
        }

        $stored = Setting::get("messaging.{$role}.messages_per_exchange");
        if ($stored !== null && $stored !== '') {
            return self::SOURCE_GLOBAL;
        }

        return self::SOURCE_DEFAULT;
    }

    /**
     * Snapshot value stored on exchanges: 0 = unlimited, >= 1 = cap.
     */
    public static function snapshotMessagesPerExchange(?Plan $plan, string $role): int
    {
        $resolved = $role === self::ROLE_PSYCHOLOGIST
            ? self::psychologistMessagesPerExchange($plan)
            : self::patientMessagesPerExchange($plan);

        return $resolved ?? 0;
    }

    protected static function roleKey(?User $user): string
    {
        if ($user?->isPsychologist()) {
            return self::ROLE_PSYCHOLOGIST;
        }

        // Default to patient rules for patients and unknown roles in chat contexts.
        return self::ROLE_PATIENT;
    }

    protected static function normalizeRole(string $role): string
    {
        return $role === self::ROLE_PSYCHOLOGIST
            ? self::ROLE_PSYCHOLOGIST
            : self::ROLE_PATIENT;
    }

    /**
     * Plan → stored global → config/messaging.php. 0 at a level skips to the next.
     */
    protected static function resolveLimit(
        ?User $user,
        string $field,
        string $legacyKey,
        int $patientConfigDefault,
    ): ?int {
        $fromPlan = self::limitFromActivePlan($user, $field);
        if (is_int($fromPlan)) {
            return $fromPlan;
        }

        $role = self::roleKey($user);
        $fromGlobal = self::storedGlobalLimit($role, $field, $legacyKey);
        if ($fromGlobal !== null) {
            return $fromGlobal;
        }

        $fromConfig = (int) config("messaging.{$role}.{$field}", 0);
        if ($fromConfig > 0) {
            return $fromConfig;
        }

        if ($role === self::ROLE_PATIENT && $patientConfigDefault > 0) {
            return $patientConfigDefault;
        }

        $legacyConfig = (int) config("messaging.{$field}", 0);

        return $legacyConfig > 0 ? $legacyConfig : null;
    }

    /**
     * Plan (including 0) → stored global (including 0) → config. 0 = unlimited (null).
     */
    protected static function resolveMessagesPerExchange(?Plan $plan, string $column, string $role): ?int
    {
        if ($plan) {
            $raw = $plan->getRawOriginal($column);
            if ($raw === null) {
                $raw = $plan->{$column};
            }
            if ($raw !== null && $raw !== '') {
                $value = (int) $raw;

                return $value > 0 ? $value : null;
            }
        }

        $stored = Setting::get("messaging.{$role}.messages_per_exchange");
        if ($stored !== null && $stored !== '') {
            $value = (int) $stored;

            return $value > 0 ? $value : null;
        }

        $fromConfig = (int) config("messaging.messages_per_exchange.{$role}", 5);

        return $fromConfig > 0 ? $fromConfig : null;
    }

    /**
     * DB setting only (admin Messaging page). 0 / empty = not set.
     */
    protected static function storedGlobalLimit(string $role, string $field, string $legacyKey): ?int
    {
        $raw = Setting::get("messaging.{$role}.{$field}");

        if ($raw === null && $role === self::ROLE_PATIENT) {
            $legacy = Setting::get($legacyKey);
            if ($legacy !== null) {
                $raw = $legacy;
            }
        }

        if ($raw === null || $raw === '') {
            return null;
        }

        $max = (int) $raw;

        return $max > 0 ? $max : null;
    }

    /**
     * Resolve an effective limit: null = unlimited.
     * Old helper: stored setting, then config (0 = unlimited, no config fallback).
     */
    protected static function limitFor(
        string $role,
        string $field,
        string $legacyRole,
        string $legacyKey,
        int $configDefault,
    ): ?int {
        $raw = Setting::get("messaging.{$role}.{$field}");

        if ($raw === null && $role === $legacyRole) {
            $legacy = Setting::get($legacyKey);
            if ($legacy !== null) {
                $raw = $legacy;
            }
        }

        if ($raw === null) {
            $raw = config("messaging.{$role}.{$field}", $configDefault);
        }

        $max = (int) $raw;

        return $max > 0 ? $max : null;
    }

    /**
     * Old combiner (unused): min(plan, global); global 0 hid the chat counter.
     *
     * $fromPlan: false = no plan value, null = plan set to 0 (unlimited), int = cap.
     * $fromGlobal: null = global 0 (unlimited), int = cap.
     */
    // protected static function combinePlanAndGlobal(int|false|null $fromPlan, ?int $fromGlobal): ?int
    // {
    //     $planCap = is_int($fromPlan) ? $fromPlan : null;
    //
    //     // Global 0 / unlimited → apply only the plan limit.
    //     if ($fromGlobal === null) {
    //         return $planCap;
    //     }
    //
    //     // No plan cap (missing plan, empty field, or plan 0) → global only.
    //     if ($planCap === null) {
    //         return $fromGlobal;
    //     }
    //
    //     // Both set → stricter of the two.
    //     return min($planCap, $fromGlobal);
    // }

    /**
     * Plan value for patients: false = no override, null = unlimited (0), int = cap.
     */
    protected static function limitFromActivePlan(?User $user, string $field): int|false|null
    {
        if (! $user?->isPatient()) {
            return false;
        }

        $user->loadMissing('patient');
        $subscription = $user->patient?->activeSubscription();
        $subscription?->loadMissing('plan');
        $plan = $subscription?->plan;

        if (! $plan) {
            return false;
        }

        $raw = $plan->getRawOriginal($field) ?? $plan->{$field};

        if ($raw === null || $raw === '') {
            return false;
        }

        $max = (int) $raw;

        return $max > 0 ? $max : null;
    }
}
