<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PlanSeeder::class,
            CountryRestrictionSeeder::class,
            AdminUserSeeder::class,
        ]);

        // Uncomment to also generate demo patients/psychologists/conversations
        // for local development:
        // \App\Models\Psychologist::factory(5)->create();
        // \App\Models\Patient::factory(20)->create();
    }
}
