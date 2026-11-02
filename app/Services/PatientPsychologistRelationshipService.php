<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Patient;
use App\Models\PatientPsychologistRelationship;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class PatientPsychologistRelationshipService
{
    public function requestConsultation(Patient $patient, Professional $psychologist): PatientPsychologistRelationship
    {
        if (! $psychologist->isListedForPatients()) {
            throw ValidationException::withMessages([
                'psychologist_id' => __('psychologist_directory_api.not_listed'),
            ]);
        }

        // Check if there is an existing non-closed relationship
        $existing = PatientPsychologistRelationship::query()
            ->where('patient_id', $patient->id)
            ->where('psychologist_id', $psychologist->id)
            ->whereNotIn('status', [
                PatientPsychologistRelationship::STATUS_REJECTED,
                PatientPsychologistRelationship::STATUS_CLOSED
            ])
            ->first();

        if ($existing) {
            return $existing;
        }

        $relationship = PatientPsychologistRelationship::create([
            'patient_id' => $patient->id,
            'psychologist_id' => $psychologist->id,
            'status' => PatientPsychologistRelationship::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        $relationship->loadMissing(['patient.user', 'psychologist.user']);
        $psychologistUser = $relationship->psychologist?->user;
        $patientName = $relationship->patient?->user?->name ?? '';

        if ($psychologistUser) {
            $payload = \App\Notifications\PatientRequestNotification::payload(
                $patientName,
                route('frontend.psychologist.dashboard'),
            );
            app(NotificationService::class)->sendPayload($psychologistUser, $payload);
        }

        return $relationship;
    }

    public function acceptRequest(PatientPsychologistRelationship $relationship): PatientPsychologistRelationship
    {
        if ($relationship->status !== PatientPsychologistRelationship::STATUS_PENDING) {
            return $relationship;
        }

        $relationship->update([
            'status' => PatientPsychologistRelationship::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        $relationship->loadMissing(['patient.user', 'psychologist.user']);

        // Point 13: accepted requests should already appear under My Conversations.
        $this->ensureConversation($relationship);
        $psychologistName = $relationship->psychologist->user->name ?? trim(($relationship->psychologist->first_name ?? '') . ' ' . ($relationship->psychologist->last_name ?? ''));

        // Custom notifications table is used (see NotificationService). Laravel
        // database notifications cannot write to that schema.
        // if ($relationship->patient && $relationship->patient->user) {
        //     $relationship->patient->user->notify(new \App\Notifications\RequestAcceptedNotification($psychologistName));
        // }

        if ($relationship->patient?->user) {
            $payload = \App\Notifications\RequestAcceptedNotification::payload(
                $psychologistName,
                $relationship->psychologist?->uuid
                    ? route('frontend.patient.psychologists.show', $relationship->psychologist->uuid)
                    : route('frontend.patient.psychologists.index'),
            );
            app(\App\Services\NotificationService::class)->send(
                $relationship->patient->user,
                $payload['type'],
                $payload['title'],
                $payload['body'],
                $payload['data'],
            );
        }

        return $relationship;
    }

    public function rejectRequest(PatientPsychologistRelationship $relationship, ?string $reason = null): PatientPsychologistRelationship
    {
        if ($relationship->status !== PatientPsychologistRelationship::STATUS_PENDING) {
            return $relationship;
        }

        $relationship->update([
            'status' => PatientPsychologistRelationship::STATUS_REJECTED,
            'closed_at' => now(),
            'close_reason' => $reason,
        ]);

        $relationship->loadMissing(['patient.user', 'psychologist.user']);
        $patientUser = $relationship->patient?->user;
        $psychologistName = $relationship->psychologist->user->name
            ?? trim(($relationship->psychologist->first_name ?? '').' '.($relationship->psychologist->last_name ?? ''));

        if ($patientUser) {
            $payload = \App\Notifications\RequestRejectedNotification::payload(
                $psychologistName,
                $reason,
                $relationship->psychologist?->uuid
                    ? route('frontend.patient.psychologists.show', $relationship->psychologist->uuid)
                    : route('frontend.patient.psychologists.index'),
            );
            app(NotificationService::class)->sendPayload($patientUser, $payload);
        }

        return $relationship;
    }

    public function markAsActive(PatientPsychologistRelationship $relationship): PatientPsychologistRelationship
    {
        if ($relationship->status === PatientPsychologistRelationship::STATUS_ACCEPTED) {
            $relationship->update([
                'status' => PatientPsychologistRelationship::STATUS_ACTIVE,
            ]);
            $this->ensureConversation($relationship);
        }

        return $relationship;
    }

    /**
     * Open (or reuse) the chat thread so both dashboards list it under
     * My Conversations as soon as the psychologist accepts.
     */
    public function ensureConversation(PatientPsychologistRelationship $relationship): Conversation
    {
        $conversation = Conversation::query()
            ->where(function ($query) use ($relationship) {
                $query->where('relationship_id', $relationship->id)
                    ->orWhere(function ($pair) use ($relationship) {
                        $pair->where('patient_id', $relationship->patient_id)
                            ->where('psychologist_id', $relationship->psychologist_id);
                    });
            })
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->first();

        if ($conversation) {
            if (! $conversation->relationship_id) {
                $conversation->update(['relationship_id' => $relationship->id]);
            }

            return $conversation;
        }

        return Conversation::create([
            'patient_id' => $relationship->patient_id,
            'psychologist_id' => $relationship->psychologist_id,
            'relationship_id' => $relationship->id,
            'status' => 'active',
            'last_message_at' => now(),
        ]);
    }

    public function getCurrentRelationshipForPatient(Patient $patient): ?PatientPsychologistRelationship
    {
        return PatientPsychologistRelationship::query()
            ->with(['psychologist.user'])
            ->where('patient_id', $patient->id)
            ->whereIn('status', [
                PatientPsychologistRelationship::STATUS_ACTIVE,
                PatientPsychologistRelationship::STATUS_ACCEPTED,
            ])
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [PatientPsychologistRelationship::STATUS_ACTIVE])
            ->orderByDesc('accepted_at')
            ->orderByDesc('id')
            ->first();
    }

    public function closeRelationship(PatientPsychologistRelationship $relationship, User $closedBy, ?string $reason = null): PatientPsychologistRelationship
    {
        if (in_array($relationship->status, [PatientPsychologistRelationship::STATUS_CLOSED, PatientPsychologistRelationship::STATUS_REJECTED])) {
            return $relationship;
        }

        \DB::transaction(function () use ($relationship, $closedBy, $reason) {
            $relationship->update([
                'status' => PatientPsychologistRelationship::STATUS_CLOSED,
                'closed_at' => now(),
                'closed_by' => $closedBy->id,
                'close_reason' => $reason,
            ]);

            // Also close associated conversations
            \App\Models\Conversation::query()
                ->where('relationship_id', $relationship->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'closed',
                ]);
        });

        return $relationship;
    }

    public function getPendingRequestsForPsychologist(Professional $psychologist): Collection
    {
        return PatientPsychologistRelationship::query()
            ->with(['patient.user'])
            ->where('psychologist_id', $psychologist->id)
            ->where('status', PatientPsychologistRelationship::STATUS_PENDING)
            ->orderBy('requested_at', 'desc')
            ->get();
    }

    public function getAcceptedRequestsForPatient(Patient $patient): Collection
    {
        return PatientPsychologistRelationship::query()
            ->with(['psychologist.user'])
            ->where('patient_id', $patient->id)
            ->where('status', PatientPsychologistRelationship::STATUS_ACCEPTED)
            ->orderByDesc('accepted_at')
            ->get();
    }
}


