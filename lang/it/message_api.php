<?php

return [
    'quota_exceeded' => 'Non ti restano scambi nell\'abbonamento attuale.',
    'exchange_message_limit' => 'Hai raggiunto il numero massimo di messaggi per questo scambio.',
    'your_messages' => 'I tuoi messaggi: :used / :limit',
    'messages_remaining' => ':count rimanenti',
    'composer_disabled_exchange_limit' => 'Non puoi inviare altri messaggi in questo scambio.',
    'psychologist_unavailable' => 'Il tuo psicologo non è al momento disponibile.',
    'channel_not_allowed' => 'Questo tipo di messaggio non è incluso nel tuo accesso attuale.',
    'channel_not_allowed_audio' => 'I messaggi vocali non sono inclusi nel tuo accesso attuale (solo testo + dettatura). Passa a Standard/Intensivo o usa un piano con audio.',
    'channel_not_allowed_text' => 'I messaggi di testo non sono inclusi nel tuo piano attuale.',
    'channel_not_allowed_dictation' => 'La dettatura non è inclusa nel tuo piano attuale.',
    'sent' => 'Messaggio inviato con successo.',
    'audio_sent' => 'Messaggio vocale inviato con successo.',
    'marked_read' => 'Messaggio contrassegnato come letto.',

    'validation' => [
        'attributes' => [
            'body' => 'messaggio',
            'audio' => 'file audio',
            'duration_seconds' => 'durata audio',
            'page' => 'pagina',
            'per_page' => 'risultati per pagina',
        ],
        'messages' => [
            'body.required' => 'Inserisci un messaggio.',
            'body.string' => 'Il messaggio deve essere un testo valido.',
            'body.max' => 'Il messaggio non può superare :max caratteri.',
            'duration_seconds.required' => 'La durata audio è obbligatoria.',
            'duration_seconds.max' => 'I messaggi audio non possono superare i :max secondi.',
            'audio.required' => 'Seleziona un file audio da caricare.',
            'audio.file' => 'L\'audio deve essere un file valido.',
            'audio.max' => 'Il file audio non può superare :max kilobyte.',
            'audio.mimetypes' => 'Il file audio deve essere di uno dei seguenti tipi: :values.',
            'audio.mimes' => 'Il file audio deve essere in uno dei seguenti formati: :values.',
            'page.integer' => 'La pagina deve essere un numero.',
            'page.min' => 'La pagina deve essere almeno :min.',
            'per_page.integer' => 'I risultati per pagina devono essere un numero.',
            'per_page.min' => 'I risultati per pagina devono essere almeno :min.',
            'per_page.max' => 'I risultati per pagina non possono superare :max.',
        ],
    ],
];
