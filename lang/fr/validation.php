<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'required_if' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'url' => 'Le champ :attribute doit être une URL valide.',
    'in' => 'Le champ :attribute sélectionné est invalide.',
    'exists' => 'Le champ :attribute sélectionné est invalide.',
    'image' => 'Le champ :attribute doit être une image.',
    'date_format' => 'Le champ :attribute ne correspond pas au format :format.',
    'unique' => 'Cette :attribute est déjà utilisée.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'date' => 'Le champ :attribute n\'est pas une date valide.',
    'after' => 'Le champ :attribute doit être postérieur à :date.',
    'after_or_equal' => 'Le champ :attribute ne peut pas être dans le passé.',
    'array' => 'Le champ :attribute doit être une liste.',
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'max' => [
        'string' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        'array' => 'Le champ :attribute ne peut pas contenir plus de :max éléments.',
        'file' => 'Le champ :attribute ne peut pas dépasser :max kilo-octets.',
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
        'place_id' => [
            'required_if' => 'Veuillez sélectionner un lieu existant.',
        ],
        'place.name' => [
            'required_if' => 'Le nom du lieu est obligatoire.',
        ],
        'place.address' => [
            'required_if' => 'L\'adresse du lieu est obligatoire.',
        ],
        'place.locality' => [
            'required_if' => 'Le NPA du lieu est obligatoire.',
        ],
        'place.city' => [
            'required_if' => 'La ville du lieu est obligatoire.',
        ],
    ],

    'attributes' => [
        'name' => 'nom',
        'surname' => 'nom de famille',
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'contact_email' => 'e-mail',
        'company_name' => 'entreprise',
        'company_id' => 'entreprise',
        'message' => 'message',
        'preferred_dates' => 'dates souhaitées',
        'preferred_dates.*' => 'date',
        'place_id' => 'lieu',
        'place.name' => 'nom du lieu',
        'place.address' => 'adresse',
        'place.locality' => 'NPA',
        'place.city' => 'ville',
        'place.room' => 'salle',
        'day' => 'date',
        'start_time' => 'heure de début',
        'end_time' => 'heure de fin',
        'link_appointment' => 'lien de prise de rendez-vous',
        'is_active' => 'collecte active',
        'logo' => 'logo',
        'color' => 'couleur principale',
        'color_secondary' => 'couleur secondaire',
        'labelled_at' => 'date de labellisation',
    ],
];
