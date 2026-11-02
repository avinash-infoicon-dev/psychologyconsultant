<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Support\PsychologistCareStatus;
use App\Services\Messaging\ExchangeQueueService;
use App\Services\Messaging\ExchangeQuotaService;
use App\Services\PatientPsychologistRelationshipService;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected ExchangeQueueService $queue,
        protected ExchangeQuotaService $quota,
        protected PatientPsychologistRelationshipService $relationships,
    ) {}

    public function index(): View
    {
        $user = auth()->user()->loadMissing(['patient.psychologist.user']);
        $patient = $user->patient;

        $conversationCount = Conversation::query()
            ->where('patient_id', $patient?->id)
            ->count();

        // Same queue pattern as the psychologist dashboard: threads waiting for a reply.
        $pendingExchanges = $this->queue->pendingForPatient($user);
        $pendingCount = $pendingExchanges->count();
        $acceptedRequests = $patient
            ? $this->relationships->getAcceptedRequestsForPatient($patient)
            : collect();
        $exchangeBalance = $this->quota->balanceForPatient($patient);

        $unreadMessageCount = $patient
            ? Message::query()
                ->where('is_read', false)
                ->where('sender_id', '!=', $user->id)
                ->whereHas('conversation', fn ($q) => $q->where('patient_id', $patient->id))
                ->count()
            : 0;

        $subscription = $patient?->lifecycleSubscription() ?? $patient?->latestSubscription();
        $subscription?->loadMissing('plan.translations');

        return view('frontend.patient.dashboard', [
            'user' => $user,
            'conversationCount' => $conversationCount,
            'unreadMessageCount' => $unreadMessageCount,
            'pendingExchanges' => $pendingExchanges,
            'pendingCount' => $pendingCount,
            'acceptedRequests' => $acceptedRequests,
            'exchangeBalance' => $exchangeBalance,
            'subscription' => $subscription,
            'care' => $this->careSummary($user, $pendingExchanges),
        ]);
    }

    /**
     * Current psychologist, last message, and the primary Continue CTA.
     *
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $pendingExchanges
     * @return array<string, mixed>
     */
    protected function careSummary($user, $pendingExchanges): array
    {
        $patient = $user->patient;
        $empty = [
            'psychologist_name' => null,
            'psychologist_uuid' => null,
            'last_message' => null,
            'last_message_at' => null,
            'response_pending' => false,
            'continue_url' => null,
            'history_url' => null,
            'profile_url' => null,
            'relationship_active' => false,
            'messaging_paused' => false,
        ];

        if (! $patient) {
            return $empty;
        }

        $relationship = $this->relationships->getCurrentRelationshipForPatient($patient);
        $professional = $relationship?->psychologist ?? $patient->psychologist;

        if (! $professional) {
            return $empty;
        }

        $conversation = Conversation::query()
            ->with(['messages' => fn ($q) => $q->latest('id')->limit(1)])
            ->where('patient_id', $patient->id)
            ->where('psychologist_id', $professional->id)
            ->orderByDesc('last_message_at')
            ->first();

        $latest = $conversation?->messages->first();
        $pendingForThis = $pendingExchanges->contains(
            fn (array $item) => ($item['uuid'] ?? null) === $conversation?->uuid
        );

        $name = $professional->user?->name
            ?? trim(($professional->first_name ?? '').' '.($professional->last_name ?? ''))
            ?: __('frontend.chat.unknown');

        $careStatus = PsychologistCareStatus::for($professional);
        $messagingPaused = PsychologistCareStatus::messagingPaused($professional);
        $historyUrl = $conversation
            ? route('frontend.patient.conversations.show', $conversation->uuid)
            : null;
        $profileUrl = PsychologistCareStatus::allowsPublicProfile($professional) && $professional->uuid
            ? route('frontend.patient.psychologists.show', $professional->uuid)
            : null;

        return [
            'psychologist_name' => $name,
            'psychologist_uuid' => $professional->uuid,
            'last_message' => $latest
                ? ($latest->type === 'audio'
                    ? __('frontend.chat.audio_message')
                    : Str::limit($latest->body ?? '—', 90))
                : null,
            'last_message_at' => $conversation?->last_message_at,
            'response_pending' => $pendingForThis,
            // Old: continue whenever a conversation and active relationship existed.
            // 'continue_url' => ($conversation && $relationship?->isActive())
            //     ? route('frontend.patient.conversations.show', $conversation->uuid)
            //     : null,
            'continue_url' => ($historyUrl && $relationship?->isActive() && ! $messagingPaused)
                ? $historyUrl
                : null,
            'history_url' => $historyUrl,
            'profile_url' => $profileUrl,
            'relationship_active' => (bool) $relationship?->isActive(),
            'messaging_paused' => $messagingPaused,
            'care_status' => $careStatus,
        ];
    }
}

