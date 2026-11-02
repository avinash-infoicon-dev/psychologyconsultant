<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /** Patients see only their own conversations; psychologists see only theirs. */
    public function viewAny(User $user): bool
    {
        return $user->isPatient() || $user->isPsychologist();
    }

    public function view(User $user, Conversation $conversation): bool
    {
        return $this->isParticipant($user, $conversation);
    }

    /** Only a patient can start a conversation (with a chosen/assigned psychologist). */
    public function create(User $user): bool
    {
        //return $user->isPatient() && $user->patient?->psychologist_id !== null;
        // Allow patients to start a conversation; ConversationService validates
        // the chosen psychologist (or falls back to the assigned one).
        return $user->isPatient();
    }

    public function update(User $user, Conversation $conversation): bool
    {
        return $this->isParticipant($user, $conversation);
    }

    /** Archiving/closing, not hard deletion — conversations are never truly deleted. */
    public function archive(User $user, Conversation $conversation): bool
    {
        return $this->isParticipant($user, $conversation);
    }

    protected function isParticipant(User $user, Conversation $conversation): bool
    {
        $user->loadMissing(['patient', 'psychologist']);

        if ($user->isPatient()) {
            return $conversation->patient_id === $user->patient?->id;
        }

        if ($user->isPsychologist()) {
            return $conversation->psychologist_id === $user->psychologist?->id;
        }

        return false;
    }
}
