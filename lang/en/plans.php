<?php

return [
    'on-demand' => [
        'name' => 'On-demand',
        'description' => 'Single exchange for non-subscribers who want to try. Text + dictation. Response ≤ 48 working hours.',
        'response_time_label' => '≤ 48 working hours',
        'features' => [
            '1 exchange (pay per use)',
            'Text + dictation',
            'Response ≤ 48 working hours',
        ],
    ],
    'essential' => [
        'name' => 'Essential',
        'description' => 'Monthly subscription with 3 included exchanges. Text + dictation. Response ≤ 48 working hours.',
        'response_time_label' => '≤ 48 working hours',
        'features' => [
            '3 exchanges / month',
            'Text + dictation',
            'Response ≤ 48 working hours',
        ],
    ],
    'standard' => [
        'name' => 'Standard',
        'description' => 'Recommended monthly plan with 6 exchanges, faster response, and audio messages.',
        'response_time_label' => '≤ 24 working hours',
        'features' => [
            '6 exchanges / month',
            'Text + dictation + audio',
            'Response ≤ 24 working hours',
            'Recommended',
        ],
    ],
    'intensive' => [
        'name' => 'Intensive',
        'description' => 'Higher monthly volume (10 exchanges, fair use) with priority response under 24 hours and audio.',
        'response_time_label' => 'priority < 24 hours',
        'features' => [
            '10 exchanges / month (fair use)',
            'Text + dictation + audio',
            'Priority response < 24 hours',
        ],
    ],
    'additional-exchange' => [
        'name' => 'Additional exchange',
        'description' => 'Extra exchange beyond the monthly quota. Response time follows the patient’s active plan.',
        'response_time_label' => 'according to plan',
        'features' => [
            '1 additional exchange',
            'Response time according to plan',
            'For quota overflow',
        ],
    ],
];
