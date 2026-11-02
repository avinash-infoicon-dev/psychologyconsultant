<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Languages
    |--------------------------------------------------------------------------
    |
    | Single source of truth for locales used across the app (admin forms,
    | navbar, login switcher, API validation, profile, etc.).
    |
    | To add/remove a language, edit this array only — then add lang/{code}
    | files and CMS/mail seeder translations (see comment block below).
    |
    | key   = locale code stored in DB / used by Laravel
    | label = display name in selects/menus
    | short = short badge (login buttons, compact UI)
    |
    */

    'supported' => [
        'fr_CH' => [
            'label' => 'Français (Suisse)',
            'short' => 'FR',
        ],
        'en' => [
            'label' => 'English',
            'short' => 'EN',
        ],
        // 'de' => [
        //     'label' => 'Deutsch',
        //     'short' => 'DE',
        // ],
        // 'it' => [
        //     'label' => 'Italiano',
        //     'short' => 'IT',
        // ],
        // Optional Swiss / alternate codes (commented — enable when needed):
        // 'de_CH' => [
        //     'label' => 'Deutsch (Schweiz)',
        //     'short' => 'DE',
        // ],
        // 'fr' => [
        //     'label' => 'Français',
        //     'short' => 'FR',
        // ],
        // 'it_CH' => [
        //     'label' => 'Italiano (Svizzera)',
        //     'short' => 'IT',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | How to add a new language
    |--------------------------------------------------------------------------
    |
    | 1. Add an entry under `supported` above (key = locale code ≤ 5 chars for users.locale).
    | 2. Create lang/{code}/ by copying lang/en/ and translate the PHP files.
    | 3. Add translations in CmsSeeder / MailTemplateSeeder content maps
    |    (they auto-filter via Locales::pick() to currently supported codes).
    | 4. Re-run seeders:
    |      php artisan db:seed --class=CmsSeeder
    |      php artisan db:seed --class=MailTemplateSeeder
    |    or: php artisan db:seed --class=LocaleContentSeeder
    |
    */

];
