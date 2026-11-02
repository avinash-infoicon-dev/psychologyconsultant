<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::query()->updateOrCreate(
            ['slug' => 'patients.view'],
            [
                'name' => 'View Patients',
                'group' => 'patients',
            ]
        );

        Role::query()
            ->whereIn('slug', ['superadmin', 'admin'])
            ->each(function (Role $role) use ($permission) {
                $role->permissions()->syncWithoutDetaching([$permission->id]);
            });
    }

    public function down(): void
    {
        $permission = Permission::query()->where('slug', 'patients.view')->first();

        if (! $permission) {
            return;
        }

        $permission->roles()->detach();
        $permission->delete();
    }
};
