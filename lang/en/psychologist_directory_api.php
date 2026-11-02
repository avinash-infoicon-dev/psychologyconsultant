<?php

return [
    'not_found' => 'Psychologist not found or not available.',
    'not_listed' => 'This psychologist is not available for new requests.',

    'validation' => [
        'attributes' => [
            'specialization' => 'specialization',
            'language' => 'language',
            'availability' => 'availability',
            'accepting_only' => 'accepting patients only',
            'page' => 'page',
            'per_page' => 'results per page',
        ],
        'messages' => [
            'specialization.string' => 'The specialization must be a valid text value.',
            'specialization.max' => 'The specialization may not be greater than :max characters.',
            'language.in' => 'The selected language is not supported.',
            'availability.in' => 'The selected availability filter is not valid.',
            'accepting_only.boolean' => 'The accepting patients only value must be true or false.',
            'page.integer' => 'The page must be a number.',
            'page.min' => 'The page must be at least :min.',
            'per_page.integer' => 'Results per page must be a number.',
            'per_page.min' => 'Results per page must be at least :min.',
            'per_page.max' => 'Results per page may not be greater than :max.',
        ],
    ],
];
