<?php

return [
    'not_found' => 'Psychologe nicht gefunden oder nicht verfügbar.',
    'not_listed' => 'Dieser Psychologe ist für neue Anfragen nicht verfügbar.',

    'validation' => [
        'attributes' => [
            'specialization' => 'Spezialisierung',
            'language' => 'Sprache',
            'availability' => 'Verfügbarkeit',
            'accepting_only' => 'nur Patienten annehmend',
            'page' => 'Seite',
            'per_page' => 'Ergebnisse pro Seite',
        ],
        'messages' => [
            'specialization.string' => 'Die Spezialisierung muss ein gültiger Textwert sein.',
            'specialization.max' => 'Die Spezialisierung darf höchstens :max Zeichen lang sein.',
            'language.in' => 'Die ausgewählte Sprache wird nicht unterstützt.',
            'availability.in' => 'Der Verfügbarkeitsfilter ist ungültig.',
            'accepting_only.boolean' => 'Der Wert «nur Patienten annehmend» muss wahr oder falsch sein.',
            'page.integer' => 'Die Seite muss eine Zahl sein.',
            'page.min' => 'Die Seite muss mindestens :min betragen.',
            'per_page.integer' => 'Ergebnisse pro Seite muss eine Zahl sein.',
            'per_page.min' => 'Ergebnisse pro Seite muss mindestens :min betragen.',
            'per_page.max' => 'Ergebnisse pro Seite darf höchstens :max betragen.',
        ],
    ],
];
