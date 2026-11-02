<?php

return [
    'quota_exceeded' => 'In Ihrem aktuellen Abonnement sind keine Austausche mehr verfügbar.',
    'exchange_message_limit' => 'Sie haben die maximale Anzahl Nachrichten für diesen Austausch erreicht.',
    'your_messages' => 'Ihre Nachrichten: :used / :limit',
    'messages_remaining' => ':count verbleibend',
    'composer_disabled_exchange_limit' => 'Sie können in diesem Austausch keine weiteren Nachrichten senden.',
    'psychologist_unavailable' => 'Ihre Psychologin oder Ihr Psychologe ist derzeit nicht verfügbar.',
    'channel_not_allowed' => 'Dieser Nachrichtentyp ist in Ihrem aktuellen Zugang nicht enthalten.',
    'channel_not_allowed_audio' => 'Sprachnachrichten sind in Ihrem aktuellen Zugang nicht enthalten (nur Text + Diktat). Wechseln Sie zu Standard/Intensiv oder nutzen Sie ein Abo mit Audio.',
    'channel_not_allowed_text' => 'Textnachrichten sind in Ihrem aktuellen Abo nicht enthalten.',
    'channel_not_allowed_dictation' => 'Diktat ist in Ihrem aktuellen Abo nicht enthalten.',
    'sent' => 'Nachricht erfolgreich gesendet.',
    'audio_sent' => 'Sprachnachricht erfolgreich gesendet.',
    'marked_read' => 'Nachricht als gelesen markiert.',

    'validation' => [
        'attributes' => [
            'body' => 'Nachricht',
            'audio' => 'Audiodatei',
            'duration_seconds' => 'Audiodauer',
            'page' => 'Seite',
            'per_page' => 'Ergebnisse pro Seite',
        ],
        'messages' => [
            'body.required' => 'Bitte geben Sie eine Nachricht ein.',
            'body.string' => 'Die Nachricht muss gültiger Text sein.',
            'body.max' => 'Die Nachricht darf höchstens :max Zeichen lang sein.',
            'duration_seconds.required' => 'Die Audiodauer ist erforderlich.',
            'duration_seconds.max' => 'Sprachnachrichten dürfen höchstens :max Sekunden lang sein.',
            'audio.required' => 'Bitte wählen Sie eine Audiodatei zum Hochladen.',
            'audio.file' => 'Das Audio muss eine gültige Datei sein.',
            'audio.max' => 'Die Audiodatei darf höchstens :max Kilobyte gross sein.',
            'audio.mimetypes' => 'Die Audiodatei muss einen der folgenden Typen haben: :values.',
            'audio.mimes' => 'Die Audiodatei muss eines der folgenden Formate haben: :values.',
            'page.integer' => 'Die Seite muss eine Zahl sein.',
            'page.min' => 'Die Seite muss mindestens :min betragen.',
            'per_page.integer' => 'Ergebnisse pro Seite muss eine Zahl sein.',
            'per_page.min' => 'Ergebnisse pro Seite muss mindestens :min betragen.',
            'per_page.max' => 'Ergebnisse pro Seite darf höchstens :max betragen.',
        ],
    ],
];
