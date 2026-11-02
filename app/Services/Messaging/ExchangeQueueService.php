<?php

namespace App\Services\Messaging;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * §5.7 — psychologist work queue: exchanges waiting for a reply,
 * sorted by overdue / remaining SLA / plan priority.
 */
class ExchangeQueueService
{
    /**
     * Pending = last message in the thread was sent by the patient
     * (psychologist has not replied yet).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function pendingForPsychologist(User $user, int $limit = 20): Collection
    {
        $user->loadMissing(['psychologist', 'professional']);
        $professionalId = $user->psychologist?->id ?? $user->professional?->id;

        if (! $professionalId) {
            return collect();
        }

        $conversations = Conversation::query()
            ->where('psychologist_id', $professionalId)
            ->with([
                'patient.user',
                'patient.subscriptions' => fn ($q) => $q->where('status', 'active')->latest()->with('plan'),
                'messages' => fn ($q) => $q->latest('id')->limit(1)->with('sender'),
            ])
            ->orderByDesc('last_message_at')
            ->limit(100)
            ->get();

        return $conversations
            ->map(fn (Conversation $conversation) => $this->mapPendingItem($conversation))
            ->filter()
            ->sort(function (array $a, array $b) {
                // Overdue first, then soonest deadline, then higher priority.
                if ($a['is_overdue'] !== $b['is_overdue']) {
                    return $a['is_overdue'] ? -1 : 1;
                }

                if ($a['remaining_seconds'] !== $b['remaining_seconds']) {
                    return $a['remaining_seconds'] <=> $b['remaining_seconds'];
                }

                return $b['priority_weight'] <=> $a['priority_weight'];
            })
            ->take($limit)
            ->values();
    }

    /**
     * Pending for the patient = last message in the thread was sent by the
     * psychologist (patient has not replied yet).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function pendingForPatient(User $user, int $limit = 20): Collection
    {
        $user->loadMissing('patient');
        $patientId = $user->patient?->id;

        if (! $patientId) {
            return collect();
        }

        $conversations = Conversation::query()
            ->where('patient_id', $patientId)
            ->where('status', 'active')
            ->with([
                'psychologist.user',
                'patient.subscriptions' => fn ($q) => $q->where('status', 'active')->latest()->with('plan'),
                'messages' => fn ($q) => $q->latest('id')->limit(1)->with('sender'),
            ])
            ->orderByDesc('last_message_at')
            ->limit(100)
            ->get();

        return $conversations
            ->map(fn (Conversation $conversation) => $this->mapPendingItemForPatient($conversation))
            ->filter()
            ->sort(function (array $a, array $b) {
                return ($b['waiting_since']?->timestamp ?? 0) <=> ($a['waiting_since']?->timestamp ?? 0);
            })
            ->take($limit)
            ->values();
    }

    protected function mapPendingItem(Conversation $conversation): ?array
    {
        $last = $conversation->messages->first();
        $patient = $conversation->patient;
        $patientUserId = $patient?->user_id;

        if (! $last || ! $patientUserId || (int) $last->sender_id !== (int) $patientUserId) {
            return null;
        }

        $plan = $patient->subscriptions->first()?->plan;
        // Default SLA when the patient has no active plan (on-demand / unknown).
        $slaHours = (int) ($plan?->response_time_hours ?: 48);
        if ($slaHours < 1) {
            $slaHours = 48;
        }

        $waitingSince = $last->created_at;
        // Calendar hours for now; working-hours calendar can replace this later.
        $deadline = $waitingSince?->copy()->addHours($slaHours);
        $remainingSeconds = $deadline ? now()->diffInSeconds($deadline, false) : 0;
        $isOverdue = $remainingSeconds < 0;

        $remainingLabel = $isOverdue
            ? __('frontend.psych_queue.overdue')
            : __('frontend.psych_queue.remaining', [
                'time' => $deadline->diffForHumans(now(), [
                    'parts' => 2,
                    'short' => true,
                    'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
                ]),
            ]);

        $priorityWeight = 0;
        if ($plan?->is_recommended) {
            $priorityWeight += 10;
        }
        if ($slaHours <= 24) {
            $priorityWeight += 20;
        }
        if (is_string($plan?->response_time_label) && str_contains(strtolower($plan->response_time_label), 'priority')) {
            $priorityWeight += 30;
        }

        $patientName = $patient->user?->name
            ?? trim(($patient->first_name ?? '').' '.($patient->last_name ?? ''))
            ?: __('frontend.chat.unknown');

        return [
            'conversation' => $conversation,
            'uuid' => $conversation->uuid,
            'patient_name' => $patientName,
            'waiting_since' => $waitingSince,
            'sla_hours' => $slaHours,
            'sla_label' => $plan?->response_time_label ?: ('≤ '.$slaHours.'h'),
            'deadline' => $deadline,
            'remaining_seconds' => $remainingSeconds,
            'remaining_label' => $remainingLabel,
            'is_overdue' => $isOverdue,
            'priority_weight' => $priorityWeight,
            'is_priority' => $priorityWeight >= 20,
            'plan_name' => $plan?->name,
            'preview' => $last->type === 'audio'
                ? __('frontend.chat.audio_message')
                : \Illuminate\Support\Str::limit((string) $last->body, 72),
        ];
    }

    protected function mapPendingItemForPatient(Conversation $conversation): ?array
    {
        $last = $conversation->messages->first();
        $psychologist = $conversation->psychologist;
        $psychologistUserId = $psychologist?->user_id ?? $psychologist?->user?->id;

        if (! $last || ! $psychologistUserId || (int) $last->sender_id !== (int) $psychologistUserId) {
            return null;
        }

        $patient = $conversation->patient;
        $plan = $patient?->subscriptions?->first()?->plan;
        $waitingSince = $last->created_at;

        $psychologistName = $psychologist->user?->name
            ?? trim(($psychologist->first_name ?? '').' '.($psychologist->last_name ?? ''))
            ?: __('frontend.chat.unknown');

        return [
            'conversation' => $conversation,
            'uuid' => $conversation->uuid,
            'psychologist_name' => $psychologistName,
            'waiting_since' => $waitingSince,
            'sla_label' => $plan?->name,
            'remaining_label' => __('frontend.psych_queue.received', [
                'time' => $waitingSince?->diffForHumans() ?? '',
            ]),
            'is_overdue' => false,
            'is_priority' => false,
            'plan_name' => $plan?->name,
            'preview' => $last->type === 'audio'
                ? __('frontend.chat.audio_message')
                : \Illuminate\Support\Str::limit((string) $last->body, 72),
        ];
    }
}
