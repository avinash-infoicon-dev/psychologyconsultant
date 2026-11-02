<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication
    |--------------------------------------------------------------------------
    */
    'two_factor' => [
        'enabled' => env('TWO_FACTOR_ENABLED', true),
        'required_for_roles' => ['admin', 'psychologist'],
        'code_length' => 6,
        'code_expiry_minutes' => 10,
        'max_attempts' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Verification (registration)
    |--------------------------------------------------------------------------
    */
    'email_verification' => [
        'code_length' => 6,
        'code_expiry_minutes' => 30,
        'max_attempts' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Field-Level Encryption
    |--------------------------------------------------------------------------
    | Model attributes that must be encrypted at rest (handled via an
    | Encryptable trait / cast rather than the app key alone).
    */
    'encrypted_fields' => [
        'messages.body',
        'clinical_notes.content',
        'consents.content',
    ],

    'audio_encryption' => [
        'enabled' => env('AUDIO_ENCRYPTION_ENABLED', true),
        'cipher' => 'AES-256-CBC',
    ],

    /*
    |--------------------------------------------------------------------------
    | Session / Login
    |--------------------------------------------------------------------------
    */
    'max_login_attempts' => env('MAX_LOGIN_ATTEMPTS', 5),
    'lockout_minutes' => env('LOGIN_LOCKOUT_MINUTES', 15),
    'session_lifetime_minutes' => env('SESSION_LIFETIME', 120),
    'force_single_device_session' => env('FORCE_SINGLE_SESSION', false),

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    */
    'password' => [
        'min_length' => 10,
        'require_uppercase' => true,
        'require_number' => true,
        'require_symbol' => true,
        'expiry_days' => env('PASSWORD_EXPIRY_DAYS', 0), // 0 disables expiry
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled' => true,
        'log_models' => [
            'ClinicalNote',
            'Consent',
            'Patient',
            'Payment',
            'Subscription',
        ],
        'retain_days' => env('AUDIT_LOG_RETENTION_DAYS', 2555), // ~7 years, common healthcare retention
    ],
];
