<?php

namespace App\Services\Messaging;

use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use App\Models\Patient;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class ConversationService
{
    public function listForUser(User $user, int $perPage = 20): LengthAwarePaginator
    {
        $query = Conversation::query()
            ->with(['patient.user', 'psychologist.user', 'messages' => fn ($q) => $q->latest()->limit(1)->with('conversation')])
            ->withCount([
                'messages as unread_count' => fn ($q) => $q
                    ->where('is_read', false)
                    ->where('sender_id', '!=', $user->id),
            ])
            ->orderByDesc('last_message_at');

        if ($user->isPatient()) {
            $query->where('patient_id', $user->patient->id);
            /* Legacy: Only get the latest conversation per psychologist
            $query->whereIn('id', function ($q) use ($user) {
                $q->selectRaw('MAX(id)')
                  ->from('conversations')
                  ->where('patient_id', $user->patient->id)
                  ->groupBy('psychologist_id');
            });
            */
        } elseif ($user->isPsychologist()) {
            $professionalId = $user->psychologist?->id ?? $user->professional?->id;
            $query->where('psychologist_id', $professionalId ?: 0);
            /* Legacy: Only get the latest conversation per patient
            $query->whereIn('id', function ($q) use ($professionalId) {
                $q->selectRaw('MAX(id)')
                  ->from('conversations')
                  ->where('psychologist_id', $professionalId ?: 0)
                  ->groupBy('patient_id');
            });
            */
        } else {
            $query->whereRaw('1 = 0');
        }

        return $query->paginate($perPage);
    }

    /** @return array{data: AnonymousResourceCollection, meta: array<string, int>} */
    public function paginatedPayload(LengthAwarePaginator $conversations): array
    {
        return [
            'data' => ConversationResource::collection($conversations),
            'meta' => [
                'current_page' => $conversations->currentPage(),
                'last_page' => $conversations->lastPage(),
                'total' => $conversations->total(),
            ],
        ];
    }

    public function loadForResource(Conversation $conversation): Conversation
    {
        return $conversation->load([
            'patient.user',
            'psychologist.user',
            'messages' => fn ($q) => $q->latest()->limit(1)->with('conversation'),
        ]);
    }

    public function loadForChat(Conversation $conversation): Conversation
    {
        return $conversation->load([
            'patient.user',
            'psychologist.user',
            'messages' => fn ($q) => $q->with(['sender', 'audio', 'conversation'])->orderBy('created_at'),
        ]);
    }

    /**
     * Start or resume a patient ↔ psychologist conversation.
     *
     * Patients can pick a verified psychologist from the directory
     * (professional UUID). If none is provided, the patient's assigned
     * psychologist is used when available.
     *
     * Choosing a psychologist also updates patients.psychologist_id.
     */
    public function findOrCreateForPatient(User $patientUser, ?string $professionalUuid = null): Conversation
    {
        $patient = $patientUser->patient;

        abort_unless($patient, 422, __('conversation_api.no_patient_profile'));

        $professional = $this->resolveProfessionalForPatient($patient, $professionalUuid);

        // Require an ACTIVE relationship
        $relationship = \App\Models\PatientPsychologistRelationship::query()
            ->where('patient_id', $patient->id)
            ->where('psychologist_id', $professional->id)
            ->where('status', \App\Models\PatientPsychologistRelationship::STATUS_ACTIVE)
            ->first();

        if (!$relationship) {
            throw ValidationException::withMessages([
                'professional_uuid' => __('relationship.requires_active_relationship'),
            ]);
        }

        // Assign as the patient's primary psychologist when starting/resuming chat.
        if ((int) $patient->psychologist_id !== (int) $professional->id) {
            $patient->update(['psychologist_id' => $professional->id]);
        }

        // Find existing ACTIVE conversation linked to this relationship
        $conversation = Conversation::query()
            ->where('relationship_id', $relationship->id)
            ->where('status', 'active')
            ->first();

        // If no active conversation exists, create a new one
        if (!$conversation) {
            $conversation = Conversation::create([
                'patient_id' => $patient->id,
                'psychologist_id' => $professional->id,
                'relationship_id' => $relationship->id,
                'status' => 'active',
                'last_message_at' => now(),
            ]);
        }

        return $this->loadForResource($conversation);
    }

    protected function resolveProfessionalForPatient(Patient $patient, ?string $professionalUuid): Professional
    {
        if ($professionalUuid) {
            $professional = Professional::query()
                ->psychologists()
                ->where('uuid', $professionalUuid)
                // Old: ->where('is_verified', true)->where('status', true)
                ->listedForPatients()
                ->first();

            if (! $professional) {
                throw ValidationException::withMessages([
                    'professional_uuid' => __('conversation_api.psychologist_unavailable'),
                ]);
            }

            return $professional;
        }

        if ($patient->psychologist_id) {
            $professional = Professional::query()
                ->psychologists()
                ->whereKey($patient->psychologist_id)
                // Old: ->where('is_verified', true)->where('status', true)
                ->listedForPatients()
                ->first();

            if ($professional) {
                return $professional;
            }
        }

        throw ValidationException::withMessages([
            'professional_uuid' => __('conversation_api.pick_psychologist'),
        ]);
    }

    public function archive(Conversation $conversation): void
    {
        $conversation->update(['status' => 'archived']);
    }

    /**
     * Lightweight header-bell payload: total unread messages + recent threads.
     *
     * @return array{total: int, conversations: list<array{uuid: string, name: string, preview: string, unread: int, href: string}>}
     */
    public function unreadSummaryFor(User $user, int $limit = 8): array
    {
        $user->loadMissing(['patient', 'psychologist', 'professional']);
        $conversations = $this->listForUser($user, $limit)->getCollection();
        $prefix = $user->isPsychologist() ? 'frontend.psychologist' : 'frontend.patient';
        $total = 0;
        $items = [];

        if ($user->isPsychologist()) {
            $psychologistId = $user->psychologist?->id ?? $user->professional?->id;
            if ($psychologistId) {
                $pendingRequests = \App\Models\PatientPsychologistRelationship::query()
                    ->with('patient.user')
                    ->where('psychologist_id', $psychologistId)
                    ->where('status', \App\Models\PatientPsychologistRelationship::STATUS_PENDING)
                    ->get();

                foreach ($pendingRequests as $req) {
                    $total++;
                    $peer = $req->patient;
                    $name = $peer?->user?->name
                        ?? trim(($peer?->first_name ?? '') . ' ' . ($peer?->last_name ?? ''))
                        ?: __('frontend.chat.unknown');
                        
                    $items[] = [
                        'uuid' => 'req-' . $req->id,
                        'name' => $name,
                        'preview' => __('relationship.request_consultation'),
                        'unread' => 1,
                        'href' => route('frontend.psychologist.relationships.index'),
                    ];
                }
            }
        }

        if ($user->isPatient()) {
            $patientId = $user->patient?->id;
            if ($patientId) {
                $acceptedRequests = \App\Models\PatientPsychologistRelationship::query()
                    ->with('psychologist.user')
                    ->where('patient_id', $patientId)
                    ->where('status', \App\Models\PatientPsychologistRelationship::STATUS_ACCEPTED)
                    ->get();

                foreach ($acceptedRequests as $req) {
                    $total++;
                    $peer = $req->psychologist;
                    $name = $peer?->user?->name
                        ?? trim(($peer?->first_name ?? '').' '.($peer?->last_name ?? ''))
                        ?: __('frontend.chat.unknown');

                    $items[] = [
                        'uuid' => 'accepted-'.$req->id,
                        'name' => $name,
                        'preview' => __('relationship.request_accepted_notification', ['name' => $name]),
                        'unread' => 1,
                        'href' => $peer?->uuid
                            ? route('frontend.patient.psychologists.show', $peer->uuid)
                            : route('frontend.patient.psychologists.index'),
                    ];
                }
            }
        }

        foreach ($conversations as $conversation) {
            $unread = (int) ($conversation->unread_count ?? 0);
            $total += $unread;

            if ($unread < 1) {
                continue;
            }

            $isPsychologist = $user->isPsychologist();
            $peer = $isPsychologist ? $conversation->patient : $conversation->psychologist;
            $name = $peer?->user?->name
                ?? trim(($peer?->first_name ?? '').' '.($peer?->last_name ?? ''))
                ?: __('frontend.chat.unknown');
            $latest = $conversation->messages->first();

            $items[] = [
                'uuid' => $conversation->uuid,
                'name' => $name,
                'preview' => $latest
                    ? ($latest->type === 'audio'
                        ? __('frontend.chat.audio_message')
                        : \Illuminate\Support\Str::limit($latest->body ?? '—', 72))
                    : __('frontend.chat.no_messages_yet'),
                'unread' => $unread,
                'href' => route($prefix.'.conversations.show', $conversation->uuid),
            ];
        }

        return [
            'total' => $total,
            'conversations' => $items,
            'inbox_href' => route($prefix.'.conversations.index'),
        ];
    }
}
