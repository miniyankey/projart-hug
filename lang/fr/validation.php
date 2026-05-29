<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'date' => 'Le champ :attribute n\'est pas une date valide.',
    'after' => 'Le champ :attribute doit être une date postérieure à aujourd\'hui.',
    'array' => 'Le champ :attribute doit être une liste.',
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'max' => [
        'string' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        'array' => 'Le champ :attribute ne peut pas contenir plus de :max éléments.',
    ],

    'custom' => [
        'message' => [
            'required_if' => 'Le message est obligatoire pour une demande de contact.',
        ],
        'preferred_dates' => [
            'required_if' => 'Veuillez indiquer au moins une date souhaitée.',
        ],
    ],

    'attributes' => [
        'name' => 'nom',
        'contact_email' => 'e-mail',
        'company_name' => 'entreprise',
        'message' => 'message',
        'preferred_dates' => 'dates souhaitées',
        'preferred_dates.*' => 'date',
    ],
];
