<?php

namespace Database\Seeders;

use App\Models\CountryRestriction;
use Illuminate\Database\Seeder;

class CountryRestrictionSeeder extends Seeder
{
    /**
     * Seeds a small starter set. In production this table is managed via
     * the admin settings screen, not re-seeded — treat this as a baseline
     * for local/dev environments only.
     */
    public function run(): void
    {
        $rows = [
            ['country_code' => 'CH', 'country_name' => 'Switzerland', 'status' => 'allowed'],
            ['country_code' => 'DE', 'country_name' => 'Germany', 'status' => 'allowed'],
            ['country_code' => 'FR', 'country_name' => 'France', 'status' => 'allowed'],
            ['country_code' => 'IT', 'country_name' => 'Italy', 'status' => 'allowed'],
            ['country_code' => 'AT', 'country_name' => 'Austria', 'status' => 'allowed'],
        ];

        foreach ($rows as $row) {
            CountryRestriction::updateOrCreate(['country_code' => $row['country_code']], $row);
        }
    }
}
