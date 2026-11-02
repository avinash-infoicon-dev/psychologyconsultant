<?php

return [
    'on-demand' => [
        'name' => 'À la demande',
        'description' => 'Échange unique pour les personnes sans abonnement qui veulent essayer. Texte + dictée. Réponse ≤ 48 heures ouvrées.',
        'response_time_label' => '≤ 48 heures ouvrées',
        'features' => [
            '1 échange (paiement à l’usage)',
            'Texte + dictée',
            'Réponse ≤ 48 heures ouvrées',
        ],
    ],
    'essential' => [
        'name' => 'Essentiel',
        'description' => 'Abonnement mensuel avec 3 échanges inclus. Texte + dictée. Réponse ≤ 48 heures ouvrées.',
        'response_time_label' => '≤ 48 heures ouvrées',
        'features' => [
            '3 échanges / mois',
            'Texte + dictée',
            'Réponse ≤ 48 heures ouvrées',
        ],
    ],
    'standard' => [
        'name' => 'Standard',
        'description' => 'Formule mensuelle recommandée avec 6 échanges, une réponse plus rapide et des messages audio.',
        'response_time_label' => '≤ 24 heures ouvrées',
        'features' => [
            '6 échanges / mois',
            'Texte + dictée + audio',
            'Réponse ≤ 24 heures ouvrées',
            'Recommandé',
        ],
    ],
    'intensive' => [
        'name' => 'Intensif',
        'description' => 'Volume mensuel plus élevé (10 échanges, usage raisonnable) avec une réponse prioritaire sous 24 heures et l’audio.',
        'response_time_label' => 'priorité < 24 heures',
        'features' => [
            '10 échanges / mois (usage raisonnable)',
            'Texte + dictée + audio',
            'Réponse prioritaire < 24 heures',
        ],
    ],
    'additional-exchange' => [
        'name' => 'Échange supplémentaire',
        'description' => 'Échange en plus du quota mensuel. Le délai de réponse suit la formule active du patient.',
        'response_time_label' => 'selon la formule',
        'features' => [
            '1 échange supplémentaire',
            'Délai de réponse selon la formule',
            'En cas de quota épuisé',
        ],
    ],
];
