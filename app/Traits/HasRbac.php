<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Admin RBAC (slug-based roles + permissions).
 *
 * Method names are prefixed with "Rbac" to avoid colliding with
 * HasRolesAndPermissions, which handles lightweight API role checks.
 */
trait HasRbac
{
    public function assignRbacRole(string|array|Role ...$roles): static
    {
        $ids = $this->resolveRbacRoleIds($roles);
        $this->roles()->syncWithoutDetaching($ids);
        $this->unsetRelation('roles');
        Cache::forget("user:{$this->id}:roles");

        return $this;
    }

    public function syncRbacRoles(array $roles): static
    {
        $ids = collect($roles)->map(function ($role) {
            if ($role instanceof Role) {
                return $role->id;
            }

            return Role::query()->where('slug', $role)->value('id')
                ?? Role::query()->where('id', $role)->value('id');
        })->filter()->values()->all();

        $this->roles()->sync($ids);
        $this->unsetRelation('roles');
        Cache::forget("user:{$this->id}:roles");

        return $this;
    }

    public function hasPermissionTo(string $permission): bool
    {
        $this->loadMissing('roles.permissions');

        if ($this->hasFullAccess()) {
            return true;
        }

        return $this->roles->contains(fn (Role $role) => $role->hasPermission($permission));
    }

    public function hasRbacRole(string|array $roles): bool
    {
        $this->loadMissing('roles');
        $roles = (array) $roles;

        return $this->roles->contains(fn (Role $role) => in_array($role->slug, $roles, true));
    }

    public function hasAnyRbacRole(array $roles): bool
    {
        return $this->hasRbacRole($roles);
    }

    public function hasFullAccess(): bool
    {
        $this->loadMissing('roles');

        return $this->roles->contains(fn (Role $role) => $role->is_full_access || in_array($role->slug, ['superadmin', 'admin'], true));
    }

    public function getAllPermissions(): Collection
    {
        if ($this->hasFullAccess()) {
            return \App\Models\Permission::query()->orderBy('group')->orderBy('name')->get();
        }

        return $this->roles
            ->loadMissing('permissions')
            ->pluck('permissions')
            ->flatten()
            ->unique('id')
            ->values();
    }

    public function primaryRole(): ?Role
    {
        $this->loadMissing('roles');

        $priority = ['superadmin', 'admin', 'psychologist', 'legal_advisor'];

        foreach ($priority as $slug) {
            $role = $this->roles->firstWhere('slug', $slug);
            if ($role) {
                return $role;
            }
        }

        return $this->roles->first();
    }

    private function resolveRbacRoleIds(array $roles): array
    {
        return collect($roles)->flatten()->map(function ($role) {
            if ($role instanceof Role) {
                return $role->id;
            }

            return Role::query()->where('slug', $role)->value('id')
                ?? Role::query()->where('id', $role)->value('id');
        })->filter()->values()->all();
    }
}
