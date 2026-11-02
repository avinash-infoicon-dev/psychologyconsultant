<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Platform Admin',
                'password' => 'ChangeMe123!',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $admin->assignRbacRole('admin');
    }
}
