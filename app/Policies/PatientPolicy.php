<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    /** Psychologists list their own patient roster; admin bypasses via Gate::before. */
    public function viewAny(User $user): bool
    {
        return $user->isPsychologist();
    }

    public function view(User $user, Patient $patient): bool
    {
        if ($user->isPatient()) {
            return $patient->user_id === $user->id;
        }

        if ($user->isPsychologist()) {
            return $this->psychologistOwnsPatient($user, $patient);
        }

        return false;
    }

    /** A patient may update their own profile; a psychologist cannot edit patient data. */
    public function update(User $user, Patient $patient): bool
    {
        return $user->isPatient() && $patient->user_id === $user->id;
    }

    /** Clinical notes are a separate, more restricted permission — see ClinicalNote checks below. */
    public function viewClinicalNotes(User $user, Patient $patient): bool
    {
        if ($user->isPatient()) {
            //return $patient->psychologist_id === $user->psychologist?->id;
            return $patient->user_id === $user->id;
        }

        if ($user->isPsychologist()) {
            //  return $patient->psychologist_id === $user->psychologist?->id;
            return $this->psychologistOwnsPatient($user, $patient);
        }

        return false;
    }

    protected function psychologistOwnsPatient(User $user, Patient $patient): bool
    {
        $professionalId = $user->psychologist?->id ?? $user->professional?->id;

        // Old: return $patient->psychologist_id === $user->psychologist?->id;
        if ($patient->psychologist_id === $professionalId) {
            return true;
        }

        return $patient->conversations()
            ->where('psychologist_id', $professionalId)
            ->exists();
    }
}
