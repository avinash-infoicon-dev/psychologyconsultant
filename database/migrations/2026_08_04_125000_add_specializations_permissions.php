<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['name' => 'View Specializations', 'slug' => 'specializations.view', 'group' => 'specializations'],
            ['name' => 'Create Specializations', 'slug' => 'specializations.create', 'group' => 'specializations'],
            ['name' => 'Edit Specializations', 'slug' => 'specializations.edit', 'group' => 'specializations'],
            ['name' => 'Delete Specializations', 'slug' => 'specializations.delete', 'group' => 'specializations'],
        ];

        $ids = [];

        foreach ($permissions as $permission) {
            $ids[] = Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            )->id;
        }

        Role::query()
            ->whereIn('slug', ['superadmin', 'admin'])
            ->each(function (Role $role) use ($ids) {
                $role->permissions()->syncWithoutDetaching($ids);
            });
    }

    public function down(): void
    {
        $slugs = [
            'specializations.view',
            'specializations.create',
            'specializations.edit',
            'specializations.delete',
        ];

        $permissions = Permission::query()->whereIn('slug', $slugs)->get();

        foreach ($permissions as $permission) {
            $permission->roles()->detach();
            $permission->delete();
        }
    }
};
