<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_full_access',
        'is_active',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_full_access' => 'boolean',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function givePermissionTo(string|array|Permission ...$permissions): static
    {
        $ids = $this->resolvePermissionIds($permissions);
        $this->permissions()->syncWithoutDetaching($ids);

        return $this;
    }

    public function syncPermissions(array $permissions): static
    {
        $ids = collect($permissions)->map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission->id;
            }

            return Permission::query()->where('slug', $permission)->value('id')
                ?? Permission::query()->where('id', $permission)->value('id');
        })->filter()->values()->all();

        $this->permissions()->sync($ids);

        return $this;
    }

    public function hasPermission(string $slug): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->is_full_access) {
            return true;
        }

        return $this->permissions->contains('slug', $slug);
    }

    private function resolvePermissionIds(array $permissions): array
    {
        $flat = collect($permissions)->flatten();

        return $flat->map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission->id;
            }

            return Permission::query()->where('slug', $permission)->value('id')
                ?? Permission::query()->where('id', $permission)->value('id');
        })->filter()->values()->all();
    }
}
