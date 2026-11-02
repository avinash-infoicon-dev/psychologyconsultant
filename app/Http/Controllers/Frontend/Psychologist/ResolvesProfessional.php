<?php

namespace App\Http\Controllers\Frontend\Psychologist;

use App\Models\User;

trait ResolvesProfessional
{
    protected function professionalId(User $user): ?int
    {
        $user->loadMissing(['psychologist', 'professional']);

        return $user->psychologist?->id ?? $user->professional?->id;
    }

    protected function professional(User $user): ?\App\Models\Professional
    {
        $id = $this->professionalId($user);

        return $id ? \App\Models\Professional::query()->find($id) : null;
    }
}
