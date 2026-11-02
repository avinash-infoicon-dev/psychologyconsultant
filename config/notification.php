<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Push Notifications (FCM)
    |--------------------------------------------------------------------------
    */
    'push' => [
        'driver' => env('PUSH_DRIVER', 'fcm'),
        'fcm' => [
            'project_id' => env('FCM_PROJECT_ID'),
            'credentials_path' => env('FCM_CREDENTIALS_PATH', storage_path('app/firebase/credentials.json')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Email
    |--------------------------------------------------------------------------
    */
    'email' => [
        'from_address' => env('MAIL_FROM_ADDRESS', 'no-reply@example.com'),
        'from_name' => env('MAIL_FROM_NAME', 'Healthcare Platform'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Daily Summary Job
    |--------------------------------------------------------------------------
    */
    'daily_summary' => [
        'enabled' => env('DAILY_SUMMARY_ENABLED', true),
        'send_time' => env('DAILY_SUMMARY_TIME', '20:00'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Channels per Notification Type
    |--------------------------------------------------------------------------
    | Keys map to notification classes (short name); values are the channels
    | Laravel's `via()` should use unless a user has overridden preferences.
    */
    'default_channels' => [
        // Chat: in-app + Reverb only. Do not email every message.
        // Old: 'new_message' => ['database', 'push', 'mail'],
        'new_message' => ['database', 'push'],
        'psychologist_response' => ['database', 'push'],
        'patient_request' => ['database'],
        'request_accepted' => ['database'],
        'request_rejected' => ['database'],
        'subscription' => ['database', 'mail'],
        'subscription_renewal' => ['database', 'mail'],
        'subscription_expired' => ['database', 'mail'],
        'exchange_quota_low' => ['database', 'mail'],
        'payment_failed' => ['database', 'mail'],
        'account_status' => ['database', 'mail'],
        'profile_approved' => ['database', 'mail'],
        'profile_rejected' => ['database', 'mail'],
        'psychologist_profile_review' => ['database', 'mail'],
        'system_alert' => ['database', 'mail'],
        'report_update' => ['database'],
        'daily_summary' => ['mail'],
        // Legacy alias kept for older callers.
        'payment' => ['database', 'mail'],
    ],

    'renewal_reminder_days' => (int) env('NOTIFICATION_RENEWAL_DAYS', 7),

    'queue' => env('NOTIFICATION_QUEUE', 'notifications'),
];
