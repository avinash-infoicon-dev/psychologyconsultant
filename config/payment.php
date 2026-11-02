<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    | Which gateway to use when none is explicitly requested by the client.
    | Supported: "stripe", "twint"
    */
    'default' => env('PAYMENT_DEFAULT_GATEWAY', 'stripe'),

    'currency' => env('PAYMENT_CURRENCY', 'CHF'),

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'api_version' => '2024-06-20',
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoicing
    |--------------------------------------------------------------------------
    */
    'invoice' => [
        'prefix' => env('INVOICE_PREFIX', 'INV'),
        //  'starting_number' => env('INVOICE_STARTING_NUMBER', 1000),
        //'due_days' => env('INVOICE_DUE_DAYS', 14),
        'starting_number' => (int) env('INVOICE_STARTING_NUMBER', 1000),
        'due_days' => (int) env('INVOICE_DUE_DAYS', 14),
        'storage_disk' => 'private',
        'storage_path' => 'reports/invoices',
    ],

    /*
    |--------------------------------------------------------------------------
    | Refunds
    |--------------------------------------------------------------------------
    */
    'refund' => [
        'window_days' => env('REFUND_WINDOW_DAYS', 14),
        'auto_approve_under' => env('REFUND_AUTO_APPROVE_UNDER', 0), // amount in smallest currency unit, 0 disables auto-approval
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    | Tolerance (in seconds) allowed between webhook timestamp and server time.
    */
    'webhook_tolerance' => env('PAYMENT_WEBHOOK_TOLERANCE', 300),
];
