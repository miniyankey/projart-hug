<?php

return [
    'after' => 'The :attribute must be a date after today.',

    'custom' => [
        'message' => [
            'required_if' => 'A message is required for a contact request.',
        ],
        'preferred_dates' => [
            'required_if' => 'Please provide at least one preferred date.',
        ],
    ],

    'attributes' => [
        'name' => 'name',
        'contact_email' => 'email',
        'company_name' => 'company',
        'message' => 'message',
        'preferred_dates' => 'preferred dates',
        'preferred_dates.*' => 'date',
    ],
];
