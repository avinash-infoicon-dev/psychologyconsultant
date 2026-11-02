<?php

return [
    'not_found' => 'Psychologue introuvable ou non disponible.',
    'not_listed' => 'Ce psychologue n’est pas disponible pour de nouvelles demandes.',

    'validation' => [
        'attributes' => [
            'specialization' => 'spécialisation',
            'language' => 'langue',
            'availability' => 'disponibilités',
            'accepting_only' => 'acceptant uniquement les patients',
            'page' => 'page',
            'per_page' => 'résultats par page',
        ],
        'messages' => [
            'specialization.string' => 'La spécialisation doit être une valeur textuelle valide.',
            'specialization.max' => 'La spécialisation ne peut pas dépasser :max caractères.',
            'language.in' => 'La langue sélectionnée n’est pas prise en charge.',
            'availability.in' => 'Le filtre de disponibilités n’est pas valide.',
            'accepting_only.boolean' => 'La valeur « acceptant uniquement les patients » doit être vraie ou fausse.',
            'page.integer' => 'La page doit être un nombre.',
            'page.min' => 'La page doit être au moins :min.',
            'per_page.integer' => 'Les résultats par page doivent être un nombre.',
            'per_page.min' => 'Les résultats par page doivent être au moins :min.',
            'per_page.max' => 'Les résultats par page ne peuvent pas dépasser :max.',
        ],
    ],
];
