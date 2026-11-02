<?php

return [
    'created' => 'Gespräch erfolgreich gestartet.',
    'no_psychologist_assigned' => 'Ihnen ist noch kein Psychologe zugewiesen.',
    'no_patient_profile' => 'Patientenprofil nicht gefunden.',
    'pick_psychologist' => 'Wählen Sie einen Psychologen aus dem Verzeichnis, um ein Gespräch zu starten.',
    'psychologist_unavailable' => 'Dieser Psychologe ist für Nachrichten nicht verfügbar.',

    'validation' => [
        'attributes' => [
            'page' => 'Seite',
            'per_page' => 'Ergebnisse pro Seite',
            'professional_uuid' => 'Psychologe',
        ],
        'messages' => [
            'page.integer' => 'Die Seite muss eine Zahl sein.',
            'page.min' => 'Die Seite muss mindestens :min betragen.',
            'per_page.integer' => 'Ergebnisse pro Seite muss eine Zahl sein.',
            'per_page.min' => 'Ergebnisse pro Seite muss mindestens :min betragen.',
            'per_page.max' => 'Ergebnisse pro Seite darf höchstens :max betragen.',
            'professional_uuid.uuid' => 'Der ausgewählte Psychologe ist ungültig.',
            'professional_uuid.exists' => 'Der ausgewählte Psychologe wurde nicht gefunden.',
        ],
    ],
];
