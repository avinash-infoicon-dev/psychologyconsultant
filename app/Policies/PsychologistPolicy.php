<?php

namespace App\Policies;

use App\Models\Psychologist;
use App\Models\User;

class PsychologistPolicy
{
    /** The public directory — any authenticated patient can browse verified psychologists. */
    public function viewAny(User $user): bool
    {
        return $user->isPatient();
    }

    public function view(User $user, Psychologist $psychologist): bool
    {
        if ($user->isPatient()) {
            return $psychologist->isListedForPatients();
        }

        return $user->isPsychologist() && $psychologist->user_id === $user->id;
    }

    public function update(User $user, Psychologist $psychologist): bool
    {
        return $user->isPsychologist() && $psychologist->user_id === $user->id;
    }

    /** Verification/acceptance toggling is admin-only (handled via Gate::before, listed for clarity). */
    public function verify(User $user, Psychologist $psychologist): bool
    {
        return $user->isAdmin();
    }

    public function suspend(User $user, Psychologist $psychologist): bool
    {
        return $user->isAdmin() || $user->hasPermissionTo('psychologists.suspend');
    }
}
