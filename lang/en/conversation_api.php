<?php

return [
    'created' => 'Conversation started successfully.',
    'no_psychologist_assigned' => 'You do not have an assigned psychologist yet.',
    'no_patient_profile' => 'Patient profile not found.',
    'pick_psychologist' => 'Choose a psychologist from the directory to start a conversation.',
    'psychologist_unavailable' => 'This psychologist is not available for messaging.',

    'validation' => [
        'attributes' => [
            'page' => 'page',
            'per_page' => 'results per page',
            'professional_uuid' => 'psychologist',
        ],
        'messages' => [
            'page.integer' => 'The page must be a number.',
            'page.min' => 'The page must be at least :min.',
            'per_page.integer' => 'Results per page must be a number.',
            'per_page.min' => 'Results per page must be at least :min.',
            'per_page.max' => 'Results per page may not be greater than :max.',
            'professional_uuid.uuid' => 'The selected psychologist is invalid.',
            'professional_uuid.exists' => 'The selected psychologist was not found.',
        ],
    ],
];
