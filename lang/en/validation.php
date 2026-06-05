<?php

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute field must be a valid email address.',
    'string' => 'The :attribute field must be a string.',
    'unique' => 'This :attribute is already taken.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'after' => 'The :attribute must be a date after today.',
    'max' => [
        'string' => 'The :attribute field must not be greater than :max characters.',
        'array' => 'The :attribute field must not have more than :max items.',
    ],
    'min' => [
        'string' => 'The :attribute field must be at least :min characters.',
    ],

    // Rules used by Password::defaults()
    'letters' => 'The :attribute field must contain at least one letter.',
    'mixed' => 'The :attribute field must contain at least one uppercase and one lowercase letter.',
    'numbers' => 'The :attribute field must contain at least one number.',
    'symbols' => 'The :attribute field must contain at least one symbol.',
    'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',

    'custom' => [
        'message' => [
            'required_if' => 'A message is required for a contact request.',
        ],
        'preferred_dates' => [
            'required_if' => 'Please provide at least one preferred date.',
        ],
        'place_id' => [
            'required_if' => 'Please select an existing location.',
        ],
        'place.name' => [
            'required_if' => 'The location name is required.',
        ],
        'place.address' => [
            'required_if' => 'The location address is required.',
        ],
        'place.locality' => [
            'required_if' => 'The location postal code is required.',
        ],
        'place.city' => [
            'required_if' => 'The location city is required.',
        ],
    ],

    'attributes' => [
        'name' => 'name',
        'surname' => 'surname',
        'email' => 'email address',
        'password' => 'password',
        'contact_email' => 'email',
        'company_name' => 'company',
        'company_id' => 'company',
        'message' => 'message',
        'preferred_dates' => 'preferred dates',
        'preferred_dates.*' => 'date',
        'place_id' => 'location',
        'place.name' => 'location name',
        'place.address' => 'address',
        'place.locality' => 'postal code',
        'place.city' => 'city',
        'place.room' => 'room',
        'day' => 'date',
        'start_time' => 'start time',
        'end_time' => 'end time',
        'link_appointment' => 'appointment link',
        'is_active' => 'active drive',
        'logo' => 'logo',
        'color' => 'primary color',
        'color_secondary' => 'secondary color',
        'labelled_at' => 'labelling date',
    ],
];
