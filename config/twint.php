<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twint API Credentials
    |--------------------------------------------------------------------------
    | Twint is a Swiss mobile payment method. These values are issued by
    | your acquiring bank / Twint merchant onboarding process.
    */
    'merchant_id' => env('TWINT_MERCHANT_ID'),
    'api_key' => env('TWINT_API_KEY'),
    'api_secret' => env('TWINT_API_SECRET'),

    'environment' => env('TWINT_ENV', 'sandbox'), // sandbox | production

    'base_url' => env('TWINT_ENV', 'sandbox') === 'production'
        ? env('TWINT_PRODUCTION_URL', 'https://api.twint.ch')
        : env('TWINT_SANDBOX_URL', 'https://api.sandbox.twint.ch'),

    'callback_url' => env('TWINT_CALLBACK_URL', '/api/webhooks/twint'),
    'timeout' => env('TWINT_TIMEOUT', 15), // seconds

    'currency' => 'CHF',
];
