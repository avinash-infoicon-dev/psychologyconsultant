<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default fallback URL
    |--------------------------------------------------------------------------
    | Used when a menu item cannot resolve its type/reference (inactive page,
    | missing category, invalid route, etc.).
    */
    'default_url' => env('CMS_DEFAULT_URL', '/'),

    'locations' => [
        'header' => 'Header',
        'footer' => 'Footer',
        'sidebar' => 'Sidebar',
    ],

    'menu_types' => [
        'page' => 'Page',
        'category' => 'Category',
        'url' => 'Custom URL',
        'route' => 'Named route',
    ],
];
