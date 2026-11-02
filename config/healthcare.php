<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Exchange / Message Quotas
    |--------------------------------------------------------------------------
    | How many message "exchanges" a patient gets per plan cycle, and what
    | happens once the quota is exhausted.
    |
    | 1 exchange = one patient request (one or more consecutive patient
    | messages) + one complete psychologist response. The exchanges table
    | is the billing unit; messages stay communication-only. Remaining is
    | included_exchanges + purchased_exchanges - consumed_exchanges.
    */
    'quota' => [
        'unit' => 'exchange', // one patient request + one psychologist reply
        'reset_cycle' => 'monthly',
        'allow_overage_purchase' => true,
        'overage_credit_pack_size' => 10,
        // Notify once per period when remaining hits this floor or this % of total.
        'low_remaining_count' => (int) env('QUOTA_LOW_REMAINING_COUNT', 2),
        'low_remaining_percent' => (int) env('QUOTA_LOW_REMAINING_PERCENT', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Clinical Notes
    |--------------------------------------------------------------------------
    */
    'clinical_notes' => [
        'editable_within_hours' => 48,
        'require_signature' => true,
        'retention_years' => env('CLINICAL_NOTES_RETENTION_YEARS', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Consent Management
    |--------------------------------------------------------------------------
    */
    'consent' => [
        'required_types' => [
            'terms_of_service',
            'privacy_policy',
            'telehealth_consent',
            'data_processing_consent',
        ],
        'reconfirm_after_days' => 365,
    ],

    /*
    |--------------------------------------------------------------------------
    | Practitioner Matching
    |--------------------------------------------------------------------------
    */
    'matching' => [
        'max_active_patients_per_psychologist' => env('MAX_PATIENTS_PER_PSYCHOLOGIST', 40),
        'auto_assign' => env('AUTO_ASSIGN_PSYCHOLOGIST', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compliance
    |--------------------------------------------------------------------------
    */
    'compliance' => [
        'data_residency' => env('DATA_RESIDENCY_REGION', 'EU'),
        'require_country_restriction_check' => true,
        'gdpr_erasure_grace_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Kept for backward compatibility. Source of truth: config/locales.php
    | (via App\Support\Locales). Prefer Locales::codes() in new code.
    |
    */
    'locales' => array_keys(
        (require __DIR__.'/locales.php')['supported'] ?? []
    ),
];
