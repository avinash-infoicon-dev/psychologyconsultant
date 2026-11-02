<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Patient;
use App\Models\PatientPsychologistRelationship;
use App\Services\Messaging\ExchangeQueueService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected ExchangeQueueService $queue) {}

    public function index(): View
    {
        // Legacy profile relation was "psychologist".
        $user = auth()->user()->loadMissing(['professional', 'psychologist']);

        $professional = $user->professional ?? $user->psychologist;
        $professionalId = $professional?->id;

        // Legacy: patients assigned on the patient record only.
        $assignedPatientCount = $professionalId
            ? Patient::query()->where('psychologist_id', $professionalId)->count()
            : 0;
        // $patientCount = $assignedPatientCount;
        $patientCount = $professionalId
            ? PatientPsychologistRelationship::query()
                ->where('psychologist_id', $professionalId)
                ->where('status', PatientPsychologistRelationship::STATUS_ACTIVE)
                ->distinct()
                ->count('patient_id')
            : 0;

        $conversationCount = $professionalId
            ? Conversation::query()->where('psychologist_id', $professionalId)->count()
            : 0;

        $unreadConversationCount = $professionalId
            ? Conversation::query()
                ->where('psychologist_id', $professionalId)
                ->whereHas('messages', fn ($q) => $q
                    ->where('is_read', false)
                    ->where('sender_id', '!=', $user->id))
                ->count()
            : 0;

        $unreadMessageCount = $professionalId
            ? Message::query()
                ->where('is_read', false)
                ->where('sender_id', '!=', $user->id)
                ->whereHas('conversation', fn ($q) => $q->where('psychologist_id', $professionalId))
                ->count()
            : 0;

        // §5.7 — queue of exchanges to process (priority + remaining SLA).
        $pendingExchanges = $this->queue->pendingForPsychologist($user);
        $pendingCount = $pendingExchanges->count();
        $overdueCount = $pendingExchanges->where('is_overdue', true)->count();

        $relationshipService = app(\App\Services\PatientPsychologistRelationshipService::class);
        $pendingRequests = $professional ? $relationshipService->getPendingRequestsForPsychologist($professional) : collect();
        $pendingRequestCount = $pendingRequests->count();

        return view('frontend.psychologist.dashboard', [
            'user' => $user,
            // Keep the existing view variable until the dashboard template is renamed.
            'psychologist' => $professional,
            'patientCount' => $patientCount,
            'assignedPatientCount' => $assignedPatientCount,
            'conversationCount' => $conversationCount,
            'unreadConversationCount' => $unreadConversationCount,
            'unreadMessageCount' => $unreadMessageCount,
            'pendingExchanges' => $pendingExchanges,
            'pendingCount' => $pendingCount,
            'overdueCount' => $overdueCount,
            'pendingRequests' => $pendingRequests,
            'pendingRequestCount' => $pendingRequestCount,
        ]);
    }
}
