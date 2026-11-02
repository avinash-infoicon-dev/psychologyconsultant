<?php

namespace App\Policies;

use App\Models\User;

/**
 * A general-purpose gate for admin-only screens that don't map to a
 * single model (dashboard, settings, reports, logs). Model-specific
 * admin actions (e.g. verifying a psychologist, refunding a payment)
 * live on that model's own policy instead. Admins already bypass every
 * policy via Gate::before() in AppServiceProvider — this class exists
 * so `$this->authorize('access', Admin::class)`-style checks have a
 * concrete target, and so intent is explicit in controllers even
 * though the bypass would allow it either way.
 */
class AdminPolicy
{
    public function access(User $user): bool
    {
        return $user->isAdmin();
    }
}
