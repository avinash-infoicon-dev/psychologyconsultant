<?php

return [
    'quota_exceeded' => 'Il ne vous reste plus d\'échanges sur votre abonnement actuel.',
    'exchange_message_limit' => 'Vous avez atteint le nombre maximal de messages pour cet échange.',
    'your_messages' => 'Vos messages : :used / :limit',
    'messages_remaining' => ':count restant(s)',
    'composer_disabled_exchange_limit' => 'Vous ne pouvez plus envoyer de messages dans cet échange.',
    'psychologist_unavailable' => 'Votre psychologue n\'est pas disponible pour le moment.',
    'channel_not_allowed' => 'Ce type de message n\'est pas inclus dans votre accès actuel.',
    'channel_not_allowed_audio' => 'Les messages vocaux ne sont pas inclus dans votre accès actuel (texte + dictée uniquement). Passez à Standard/Intensif ou utilisez un forfait avec audio.',
    'channel_not_allowed_text' => 'Les messages texte ne sont pas inclus dans votre forfait actuel.',
    'channel_not_allowed_dictation' => 'La dictée n\'est pas incluse dans votre forfait actuel.',
    'sent' => 'Message envoyé avec succès.',
    'audio_sent' => 'Message vocal envoyé avec succès.',
    'marked_read' => 'Message marqué comme lu.',

    'validation' => [
        'attributes' => [
            'body' => 'message',
            'audio' => 'fichier audio',
            'duration_seconds' => 'durée audio',
            'page' => 'page',
            'per_page' => 'résultats par page',
        ],
        'messages' => [
            'body.required' => 'Veuillez saisir un message.',
            'body.string' => 'Le message doit être un texte valide.',
            'body.max' => 'Le message ne peut pas dépasser :max caractères.',
            'duration_seconds.required' => 'La durée audio est obligatoire.',
            'duration_seconds.max' => 'Les messages audio ne peuvent pas dépasser :max secondes.',
            'audio.required' => 'Veuillez sélectionner un fichier audio à téléverser.',
            'audio.file' => 'L\'audio doit être un fichier valide.',
            'audio.max' => 'Le fichier audio ne peut pas dépasser :max kilo-octets.',
            'audio.mimetypes' => 'Le fichier audio doit être l\'un des types suivants : :values.',
            'audio.mimes' => 'Le fichier audio doit être l\'un des formats suivants : :values.',
            'page.integer' => 'La page doit être un nombre.',
            'page.min' => 'La page doit être au moins :min.',
            'per_page.integer' => 'Les résultats par page doivent être un nombre.',
            'per_page.min' => 'Les résultats par page doivent être au moins :min.',
            'per_page.max' => 'Les résultats par page ne peuvent pas dépasser :max.',
        ],
    ],
];
