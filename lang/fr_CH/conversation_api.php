<?php

return [
    'created' => 'Conversation démarrée avec succès.',
    'no_psychologist_assigned' => 'Vous n\'avez pas encore de psychologue assigné.',
    'no_patient_profile' => 'Profil patient introuvable.',
    'pick_psychologist' => 'Choisissez un psychologue dans l\'annuaire pour démarrer une conversation.',
    'psychologist_unavailable' => 'Ce psychologue n\'est pas disponible pour la messagerie.',

    'validation' => [
        'attributes' => [
            'page' => 'page',
            'per_page' => 'résultats par page',
            'professional_uuid' => 'psychologue',
        ],
        'messages' => [
            'page.integer' => 'La page doit être un nombre.',
            'page.min' => 'La page doit être au moins :min.',
            'per_page.integer' => 'Les résultats par page doivent être un nombre.',
            'per_page.min' => 'Les résultats par page doivent être au moins :min.',
            'per_page.max' => 'Les résultats par page ne peuvent pas dépasser :max.',
            'professional_uuid.uuid' => 'Le psychologue sélectionné est invalide.',
            'professional_uuid.exists' => 'Le psychologue sélectionné est introuvable.',
        ],
    ],
];
