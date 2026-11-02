<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->string('verification_status', 32)->default('draft')->after('is_verified');
            $table->text('verification_reason')->nullable()->after('verification_status');
            $table->index('verification_status');
        });

        DB::table('professionals')->where('is_verified', true)->update([
            'verification_status' => 'verified',
            'status' => true,
            'verification_reason' => null,
        ]);

        DB::table('professionals')->where('is_verified', false)->update([
            'verification_status' => 'submitted',
            'status' => false,
        ]);

        $permission = Permission::query()->updateOrCreate(
            ['slug' => 'psychologists.suspend'],
            [
                'name' => 'Suspend Psychologists',
                'group' => 'psychologists',
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
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropIndex(['verification_status']);
            $table->dropColumn(['verification_status', 'verification_reason']);
        });

        $permission = Permission::query()->where('slug', 'psychologists.suspend')->first();
        if ($permission) {
            $permission->roles()->detach();
            $permission->delete();
        }
    }
};
