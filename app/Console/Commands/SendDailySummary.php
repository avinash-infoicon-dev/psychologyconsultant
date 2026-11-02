<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DailySummaryNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendDailySummary extends Command
{
    protected $signature = 'healthcare:send-daily-summary';

    protected $description = 'Email a daily activity summary to patients and psychologists with unread messages.';

    public function handle(NotificationService $notifications): int
    {
        if (! config('notification.daily_summary.enabled', true)) {
            return self::SUCCESS;
        }

        User::query()
            ->whereHas('patient')
            ->orWhereHas('psychologist')
            ->orWhereHas('professional')
            ->with(['patient', 'psychologist', 'professional'])
            ->chunk(200, function ($users) use ($notifications) {
                foreach ($users as $user) {
                    $unread = $this->unreadCountFor($user);
                    $pendingExchanges = $this->pendingExchangeCountFor($user);
                    $forPsychologist = (bool) ($user->psychologist || $user->professional);

                    // Psychologists: use pending exchanges; patients: unread messages.
                    $reportCount = $forPsychologist ? $pendingExchanges : $unread;

                    //if ($unread === 0) {
                    if ($reportCount === 0) {
                        continue; // don't email people with nothing to report
                    }

                    //$payload = DailySummaryNotification::for(['unread_messages' => $unread]);
                    $payload = DailySummaryNotification::for([
                        'unread_messages' => $unread,
                        'pending_exchanges' => $pendingExchanges,
                        'for_psychologist' => $forPsychologist,
                    ]);

                    $notifications->send(
                        $user,
                        $payload['type'],
                        $payload['title'],
                        $payload['body'],
                        $payload['data'],
                    );
                }
            });

        return self::SUCCESS;
    }

    protected function unreadCountFor(User $user): int
    {
        if ($user->patient) {
            return \App\Models\Message::whereHas('conversation', fn ($q) => $q->where('patient_id', $user->patient->id))
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->count();
        }

        if ($user->psychologist) {
            return \App\Models\Message::whereHas('conversation', fn ($q) => $q->where('psychologist_id', $user->psychologist->id))
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->count();
        }

        if ($user->professional) {
            return \App\Models\Message::whereHas('conversation', fn ($q) => $q->where('psychologist_id', $user->professional->id))
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->count();
        }

        return 0;
    }

    /** §5.6 — psychologist daily digest counts conversations awaiting a reply. */
    protected function pendingExchangeCountFor(User $user): int
    {
        $professionalId = $user->psychologist?->id ?? $user->professional?->id;

        if (! $professionalId) {
            return 0;
        }

        return \App\Models\Conversation::query()
            ->where('psychologist_id', $professionalId)
            ->with(['patient', 'messages' => fn ($q) => $q->latest('id')->limit(1)])
            ->get()
            ->filter(function ($conversation) {
                $last = $conversation->messages->first();
                $patientUserId = $conversation->patient?->user_id;

                return $last && $patientUserId && (int) $last->sender_id === (int) $patientUserId;
            })
            ->count();
    }
}
