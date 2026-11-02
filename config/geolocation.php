<?php

return [
    /*
    |--------------------------------------------------------------------------
    | IP Geolocation Provider
    |--------------------------------------------------------------------------
    */
    'driver' => env('GEO_DRIVER', 'ipinfo'), // ipinfo (dependency-free, default) | maxmind (needs geoip2/geoip2 + a database file)

    'maxmind' => [
        'database_path' => env('MAXMIND_DB_PATH', storage_path('app/geo/GeoLite2-Country.mmdb')),
    ],

    'ipinfo' => [
        'token' => env('IPINFO_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compliance / Geoblocking
    |--------------------------------------------------------------------------
    | Countries in `blocked` are denied access outright. `restricted` countries
    | may access read-only / limited features (configurable per module).
    */
    'blocked_countries' => explode(',', env('GEO_BLOCKED_COUNTRIES', '')),
    'restricted_countries' => explode(',', env('GEO_RESTRICTED_COUNTRIES', '')),

    'allow_vpn' => env('GEO_ALLOW_VPN', false),

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    | How long (in minutes) to cache a resolved IP -> country lookup.
    */
    'cache_ttl_minutes' => env('GEO_CACHE_TTL', 1440),
];
