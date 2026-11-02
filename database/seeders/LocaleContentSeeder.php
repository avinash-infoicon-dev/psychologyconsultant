<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeds / refreshes locale-dependent DB content (CMS + mail templates)
 * for all languages enabled in config/locales.php.
 *
 * Usage:
 *   php artisan db:seed --class=LocaleContentSeeder
 */
class LocaleContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CmsSeeder::class,
            CmsSiteSettingSeeder::class,
            HomeSliderSeeder::class,
            CmsShowcaseSeeder::class,
            BlogSeeder::class,
            MailTemplateSeeder::class,
        ]);
    }
}
