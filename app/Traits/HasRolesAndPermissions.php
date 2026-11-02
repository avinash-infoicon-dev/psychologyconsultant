<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

/**
 * Lightweight, dependency-free role handling for the User model.
 *
 * This intentionally mirrors the request-scoped-cache pattern used in the
 * standalone ERP RBAC system: role names for a user are resolved once per
 * request and cached, rather than re-querying on every hasRole() call.
 *
 * Kept deliberately simple here (roles only, no granular permissions table)
 * since this platform only needs three roles: patient, psychologist, admin.
 * If finer-grained permissions are needed later, extend this trait rather
 * than replacing it, so the request-cache behavior is preserved.
 */
trait HasRolesAndPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /** Role names for this user, cached for the lifetime of the request. */
    public function roleNames(): array
    {
        return Cache::remember(
            "user:{$this->id}:roles",
            now()->addMinutes(5),
            fn () => $this->roles()->pluck('name')->all()
        );
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roleNames(), true);
    }

    public function hasAnyRole(array $roles): bool
    {
        return count(array_intersect($roles, $this->roleNames())) > 0;
    }

    public function assignRole(string $roleName): void
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $this->roles()->syncWithoutDetaching([$role->id]);
        Cache::forget("user:{$this->id}:roles");
    }

    public function removeRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();

        if ($role) {
            $this->roles()->detach($role->id);
            Cache::forget("user:{$this->id}:roles");
        }
    }
}
