<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingSeeder::class,
            PlanSeeder::class,
            CountryRestrictionSeeder::class,
            //AdminUserSeeder::class,
            MailTemplateSeeder::class,
            ConversationSeeder::class,
            CmsSeeder::class,
            CmsSiteSettingSeeder::class,
            HomeSliderSeeder::class,
            CmsShowcaseSeeder::class,
            BlogSeeder::class,
            ProfessionalSeeder::class,

            // Refresh CMS + mail template translations for enabled locales:
            LocaleContentSeeder::class,
        ]);

        // \App\Models\Psychologist::factory(5)->create();
        // Professional records are created by ProfessionalSeeder above.
        // \App\Models\Patient::factory(20)->create();
    }
}
