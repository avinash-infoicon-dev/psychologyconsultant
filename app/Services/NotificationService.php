<?php

namespace App\Services;

use App\Events\NotificationPosted;
use App\Models\Notification as NotificationRecord;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Types whose email (and in-app) delivery cannot be turned off.
     *
     * @var list<string>
     */
    public const LOCKED_TYPES = [
        'payment_failed',
        'subscription_expired',
        'account_status',
        'profile_rejected',
    ];

    public function __construct(protected FcmService $fcm) {}

    /**
     * Persist a notification record and dispatch it on whichever channels
     * apply for this type, per config('notification.default_channels').
     * This is the single entry point — Notification classes, listeners,
     * and scheduled jobs should all call through here rather than
     * touching FcmService/Mail directly, so every notification shows up
     * consistently in the user's notification center.
     *
     * Reverb is always used for in-app types (private user channel `.notification`).
     * Email is queued. Chat types are database (+ Reverb), not mail.
     */
    public function send(
        User $user,
        string $type,
        string $title,
        string $body,
        array $data = [],
        ?string $dedupeKey = null,
    ): ?NotificationRecord {
        // Inactive / suspended psychologists must not receive account status
        // (suspend, restore, active, inactive) notifications.
        if (
            in_array($type, ['account_status'], true)
            && $user->isPsychologist()
            && (! $user->is_active || $user->isPsychologistSuspended())
        ) {
            return null;
        }
        // Old: account_status was always delivered, including to locked accounts.

        if ($dedupeKey) {
            $existing = $user->notifications()->where('dedupe_key', $dedupeKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        $configured = config("notification.default_channels.{$type}", ['database']);
        $mailTitle = isset($data['mail_title']) ? (string) $data['mail_title'] : $title;
        $mailBody = isset($data['mail_body']) ? (string) $data['mail_body'] : $body;
        $persistData = $data;
        unset($persistData['mail_title'], $persistData['mail_body']);

        $locked = $this->isLocked($type);
        // Old: per-user NotificationPreference rows on the profile page.
        // $prefs = $this->preferencesFor($user);
        // Old: admin channelSettings() always won for every user.
        // $prefs = $this->channelSettings();
        // $group = $this->typeGroup($type);
        // $allowInApp = $locked || $group === null || (bool) ($prefs[$group.'_in_app'] ?? true);
        // $allowEmail = $locked || $group === null || (bool) ($prefs[$group.'_email'] ?? true);
        $allowInApp = $locked || $this->channelAllowed($user, $type, 'in_app');
        $allowEmail = $locked || $this->channelAllowed($user, $type, 'email');

        $useDatabase = in_array('database', $configured, true) && $allowInApp;
        $usePush = in_array('push', $configured, true) && $allowInApp;
        $useMail = in_array('mail', $configured, true) && $allowEmail;

        if (! $useDatabase && ! $usePush && ! $useMail) {
            return null;
        }

        $channels = array_values(array_filter([
            $useDatabase ? 'database' : null,
            $usePush ? 'push' : null,
            $useMail ? 'mail' : null,
        ]));

        $record = null;

        if ($useDatabase || $usePush) {
            try {
                $record = $user->notifications()->create([
                    'type' => $type,
                    'dedupe_key' => $dedupeKey,
                    'title' => $title,
                    'body' => $body,
                    // 'data' => $data,
                    'data' => $persistData,
                    'channels' => $channels,
                ]);
            } catch (QueryException $e) {
                if ($dedupeKey) {
                    $existing = $user->notifications()->where('dedupe_key', $dedupeKey)->first();
                    if ($existing) {
                        return $existing;
                    }
                }

                throw $e;
            }
        }

        if ($record && $useDatabase) {
            $unread = $user->notifications()->whereNull('read_at')->count();
            event(new NotificationPosted($user, $record, $unread));
        }

        if ($usePush) {
            //$this->sendPush($user, $title, $body, $data);
            $this->sendPush($user, $title, $body, $persistData);
        }

        if ($useMail) {
            // Prefer confidential mail copy when provided (§5.6).
            // Old: $this->sendEmail($user, $type, $title, $body, $data);
            $this->sendEmail($user, $type, $mailTitle, $mailBody, $persistData);
        }

        return $record;
    }

    /**
     * @param  array{type: string, title: string, body: string, data?: array<string, mixed>}  $payload
     */
    public function sendPayload(User $user, array $payload, ?string $dedupeKey = null): ?NotificationRecord
    {
        return $this->send(
            $user,
            $payload['type'],
            $payload['title'],
            $payload['body'],
            $payload['data'] ?? [],
            $dedupeKey,
        );
    }

    /**
     * @param  array{type: string, title: string, body: string, data?: array<string, mixed>}  $payload
     */
    public function notifyAdmins(array $payload, ?string $dedupeKey = null): void
    {
        foreach ($this->adminUsers() as $admin) {
            $key = $dedupeKey ? $dedupeKey.':'.$admin->id : null;
            $this->sendPayload($admin, $payload, $key);
        }
    }

    /**
     * @return array{unread_count: int, notifications: list<array<string, mixed>>}
     */
    public function inboxSummary(User $user, int $limit = 8): array
    {
        $unread = $user->notifications()->whereNull('read_at')->count();
        $items = $user->notifications()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (NotificationRecord $notification) => $this->toFeedItem($notification))
            ->values()
            ->all();

        return [
            'unread_count' => $unread,
            'notifications' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toFeedItem(NotificationRecord $notification): array
    {
        $data = is_array($notification->data) ? $notification->data : [];

        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $notification->localizedTitle(),
            'body' => $notification->localizedBody(),
            'url' => $data['url'] ?? null,
            'conversation_id' => $data['conversation_id'] ?? null,
            'read_at' => $notification->read_at?->toIso8601String(),
            'created_at' => $notification->created_at?->toIso8601String(),
            'created_at_human' => $notification->created_at?->diffForHumans(),
            'data' => $data,
        ];
    }

    public function markAllRead(User $user): int
    {
        return $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function markConversationNotificationsRead(User $user, string $conversationUuid): int
    {
        if ($conversationUuid === '') {
            return 0;
        }

        return $user->notifications()
            ->whereNull('read_at')
            ->whereIn('type', ['new_message', 'psychologist_response'])
            ->where('data->conversation_id', $conversationUuid)
            ->update(['read_at' => now()]);
    }

    /**
     * Platform-wide in-app / email toggles set by admin, plus apply/visible flags.
     *
     * `{group}_apply` = admin forces the in-app/email values for everyone.
     * `{group}_visible` = the type is shown on patient/psychologist preference pages.
     *
     * @return array<string, bool>
     */
    public function channelSettings(): array
    {
        $stored = \App\Models\Setting::get('notifications.channels', []);
        if (! is_array($stored)) {
            $stored = [];
        }

        $channels = array_merge(NotificationPreference::defaults(), $this->adminControlDefaults());
        foreach ($channels as $key => $default) {
            if (array_key_exists($key, $stored)) {
                $channels[$key] = (bool) $stored[$key];
            }
        }

        $channels['account_email'] = true;

        return $channels;
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, bool>
     */
    public function updateChannelSettings(array $input): array
    {
        $channels = $this->channelSettings();

        foreach (array_keys($channels) as $key) {
            if (array_key_exists($key, $input)) {
                $channels[$key] = (bool) $input[$key];
            }
        }

        $channels['account_email'] = true;

        \App\Models\Setting::set('notifications.channels', $channels, 'json', 'notifications');

        return $channels;
    }

    /**
     * Groups patients and psychologists can be shown. Marketing stays admin-only.
     *
     * @return list<array{group: string, email_locked: bool}>
     */
    public function preferenceGroups(): array
    {
        return [
            ['group' => 'new_message', 'email_locked' => false],
            ['group' => 'subscription', 'email_locked' => false],
            ['group' => 'exchange', 'email_locked' => false],
            ['group' => 'account', 'email_locked' => true],
            // ['group' => 'marketing', 'email_locked' => false],
        ];
    }

    /**
     * Rows for the patient/psychologist preference table (visible types only).
     *
     * @return list<array{
     *     group: string,
     *     email_locked: bool,
     *     applied: bool,
     *     in_app_locked: bool,
     *     in_app: bool,
     *     email: bool
     * }>
     */
    public function userPreferenceRows(User $user): array
    {
        $admin = $this->channelSettings();
        $prefs = $this->preferencesFor($user);
        $rows = [];

        foreach ($this->preferenceGroups() as $groupRow) {
            $group = $groupRow['group'];
            if (empty($admin[$group.'_visible'])) {
                continue;
            }

            $applied = ! empty($admin[$group.'_apply']);
            $inAppKey = $group.'_in_app';
            $emailKey = $group.'_email';
            $emailLocked = $groupRow['email_locked'] || $applied;

            $rows[] = [
                'group' => $group,
                'email_locked' => $emailLocked,
                'applied' => $applied,
                'in_app_locked' => $applied,
                'in_app' => $applied
                    ? (bool) ($admin[$inAppKey] ?? true)
                    : (bool) $prefs->{$inAppKey},
                'email' => $emailLocked
                    ? ($groupRow['email_locked'] ? true : (bool) ($admin[$emailKey] ?? true))
                    : (bool) $prefs->{$emailKey},
            ];
        }

        return $rows;
    }

    /**
     * Preference columns the user may change (visible and not globally applied).
     *
     * @return list<string>
     */
    public function editablePreferenceKeys(User $user): array
    {
        $keys = [];

        foreach ($this->userPreferenceRows($user) as $row) {
            if ($row['applied']) {
                continue;
            }

            $keys[] = $row['group'].'_in_app';
            if (! $row['email_locked']) {
                $keys[] = $row['group'].'_email';
            }
        }

        return $keys;
    }

    public function preferencesFor(User $user): NotificationPreference
    {
        if ($user->relationLoaded('notificationPreference') && $user->notificationPreference) {
            return $user->notificationPreference;
        }

        return $user->notificationPreference()->firstOrCreate(
            ['user_id' => $user->id],
            NotificationPreference::defaults(),
        );
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function updatePreferences(User $user, array $input): NotificationPreference
    {
        $prefs = $this->preferencesFor($user);
        $allowed = array_keys(NotificationPreference::defaults());
        $updates = [];

        $editable = $this->editablePreferenceKeys($user);

        foreach ($allowed as $key) {
            if (! in_array($key, $editable, true)) {
                continue;
            }
            if (array_key_exists($key, $input)) {
                $updates[$key] = (bool) $input[$key];
            }
        }

        // Payment / account / security email cannot be turned off.
        $updates['account_email'] = true;

        $prefs->fill($updates);
        $prefs->save();

        return $prefs->fresh();
    }

    public function isLocked(string $type): bool
    {
        return in_array($type, self::LOCKED_TYPES, true);
    }

    /**
     * Preference column prefix, or null when the type has no user toggle.
     */
    public function typeGroup(string $type): ?string
    {
        return match ($type) {
            'new_message', 'psychologist_response', 'patient_request', 'request_accepted', 'request_rejected' => 'new_message',
            'subscription', 'subscription_renewal', 'subscription_expired', 'payment_failed' => 'subscription',
            'exchange_quota_low' => 'exchange',
            'account_status', 'profile_approved', 'profile_rejected' => 'account',
            default => null,
        };
    }

    /**
     * Admin-forced value when Apply globally is on; otherwise the user's preference.
     */
    public function channelAllowed(User $user, string $type, string $channel): bool
    {
        $group = $this->typeGroup($type);
        if ($group === null) {
            return true;
        }

        $key = $group.'_'.$channel;
        if ($key === 'account_email') {
            return true;
        }

        $admin = $this->channelSettings();

        if (! empty($admin[$group.'_apply'])) {
            return (bool) ($admin[$key] ?? true);
        }

        $prefs = $this->preferencesFor($user);

        return (bool) ($prefs->{$key} ?? true);
    }

    /**
     * @return array<string, bool>
     */
    protected function adminControlDefaults(): array
    {
        $controls = [];

        foreach (['new_message', 'subscription', 'exchange', 'account', 'marketing'] as $group) {
            // Existing installs already use admin values for everyone.
            $controls[$group.'_apply'] = true;
            $controls[$group.'_visible'] = $group !== 'marketing';
        }

        return $controls;
    }

    /**
     * @return Collection<int, User>
     */
    public function adminUsers(): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->whereIn('slug', ['admin', 'superadmin']))
            ->get();
    }

    protected function sendPush(User $user, string $title, string $body, array $data): void
    {
        $tokens = $user->devices()->whereNotNull('fcm_token')->pluck('fcm_token')->all();

        if (empty($tokens)) {
            return;
        }

        $this->fcm->sendToTokens($tokens, $title, $body, $data);
    }

    protected function sendEmail(User $user, string $type, string $title, string $body, array $data): void
    {
        // Queued mailable — chat HTTP never waits on SMTP.
        // SendEmailNotification::dispatch($user, $title, $body, $data);
        Mail::to($user->email)->queue(
            new \App\Mail\GenericNotificationMail($title, $body, $data)
        );
    }
}
