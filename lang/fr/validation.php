<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'unique' => 'Cette :attribute est déjà utilisée.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'date' => 'Le champ :attribute n\'est pas une date valide.',
    'after' => 'Le champ :attribute doit être une date postérieure à aujourd\'hui.',
    'array' => 'Le champ :attribute doit être une liste.',
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'max' => [
        'string' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        'array' => 'Le champ :attribute ne peut pas contenir plus de :max éléments.',
    ],
    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],

    // Règles utilisées par Password::defaults()
    'letters' => 'Le champ :attribute doit contenir au moins une lettre.',
    'mixed' => 'Le champ :attribute doit contenir au moins une majuscule et une minuscule.',
    'numbers' => 'Le champ :attribute doit contenir au moins un chiffre.',
    'symbols' => 'Le champ :attribute doit contenir au moins un symbole.',
    'uncompromised' => 'Le :attribute saisi est apparu dans une fuite de données. Veuillez en choisir un autre.',

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
        'surname' => 'nom de famille',
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'contact_email' => 'e-mail',
        'company_name' => 'entreprise',
        'message' => 'message',
        'preferred_dates' => 'dates souhaitées',
        'preferred_dates.*' => 'date',
    ],
];
