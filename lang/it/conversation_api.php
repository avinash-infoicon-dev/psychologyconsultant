<?php

return [
    'created' => 'Conversazione avviata con successo.',
    'no_psychologist_assigned' => 'Non hai ancora uno psicologo assegnato.',
    'no_patient_profile' => 'Profilo paziente non trovato.',
    'pick_psychologist' => 'Scegli uno psicologo dall\'elenco per avviare una conversazione.',
    'psychologist_unavailable' => 'Questo psicologo non è disponibile per i messaggi.',

    'validation' => [
        'attributes' => [
            'page' => 'pagina',
            'per_page' => 'risultati per pagina',
            'professional_uuid' => 'psicologo',
        ],
        'messages' => [
            'page.integer' => 'La pagina deve essere un numero.',
            'page.min' => 'La pagina deve essere almeno :min.',
            'per_page.integer' => 'I risultati per pagina devono essere un numero.',
            'per_page.min' => 'I risultati per pagina devono essere almeno :min.',
            'per_page.max' => 'I risultati per pagina non possono superare :max.',
            'professional_uuid.uuid' => 'Lo psicologo selezionato non è valido.',
            'professional_uuid.exists' => 'Lo psicologo selezionato non è stato trovato.',
        ],
    ],
];
