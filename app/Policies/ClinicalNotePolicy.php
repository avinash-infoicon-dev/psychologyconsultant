<?php

namespace App\Policies;

use App\Models\ClinicalNote;
use App\Models\User;

class ClinicalNotePolicy
{
    public function view(User $user, ClinicalNote $note): bool
    {
        if ($user->isAdmin() || $user->hasFullAccess() || $user->hasPermissionTo('patients.view')) {
            return true;
        }

        if ($user->isPatient()) {
            return $note->patient->user_id === $user->id;
        }

        if ($user->isPsychologist()) {
            //return $note->psychologist->user_id === $user->id;
            $professionalId = $user->psychologist?->id ?? $user->professional?->id;

            return (int) $note->psychologist_id === (int) $professionalId;
        }

        return false;
    }

    /** Only a psychologist may write clinical notes, and only for their own patients. */
    public function create(User $user, \App\Models\Patient $patient): bool
    {
        //return $user->isPsychologist()
        //&& $patient->psychologist_id === $user->psychologist?->id;
        $professionalId = $user->psychologist?->id ?? $user->professional?->id;

        if (! $user->isPsychologist() || ! $professionalId) {
            return false;
        }

        // Old: && $patient->psychologist_id === $user->psychologist?->id;
        if ($patient->psychologist_id === $professionalId) {
            return true;
        }

        return $patient->conversations()
            ->where('psychologist_id', $professionalId)
            ->exists();
    }

    /** Editable only by the authoring psychologist, and only within the configured edit window. */
    public function update(User $user, ClinicalNote $note): bool
    {
        $professionalId = $user->psychologist?->id ?? $user->professional?->id;

        return $user->isPsychologist()
        // && $note->psychologist->user_id === $user->id
            && $professionalId
            && (int) $note->psychologist_id === (int) $professionalId
            && $note->isEditable();
    }

    /** Clinical notes are never hard-deleted — signed records must persist for retention compliance. */
    public function delete(User $user, ClinicalNote $note): bool
    {
        return false;
    }
}
