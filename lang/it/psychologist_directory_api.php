<?php

return [
    'not_found' => 'Psicologo non trovato o non disponibile.',
    'not_listed' => 'Questo psicologo non è disponibile per nuove richieste.',

    'validation' => [
        'attributes' => [
            'specialization' => 'specializzazione',
            'language' => 'lingua',
            'availability' => 'disponibilità',
            'accepting_only' => 'solo accettazione pazienti',
            'page' => 'pagina',
            'per_page' => 'risultati per pagina',
        ],
        'messages' => [
            'specialization.string' => 'La specializzazione deve essere un testo valido.',
            'specialization.max' => 'La specializzazione non può superare :max caratteri.',
            'language.in' => 'La lingua selezionata non è supportata.',
            'availability.in' => 'Il filtro di disponibilità non è valido.',
            'accepting_only.boolean' => 'Il valore di sola accettazione pazienti deve essere vero o falso.',
            'page.integer' => 'La pagina deve essere un numero.',
            'page.min' => 'La pagina deve essere almeno :min.',
            'per_page.integer' => 'I risultati per pagina devono essere un numero.',
            'per_page.min' => 'I risultati per pagina devono essere almeno :min.',
            'per_page.max' => 'I risultati per pagina non possono superare :max.',
        ],
    ],
];
