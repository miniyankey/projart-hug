<?php

return [
    // Shared footer for every mail
    'footer' => [
        'signature' => 'Blood Transfusion Centre - HUG',
        'disclaimer' => 'You are receiving this message as part of the corporate blood donation programme.',
    ],

    // Mail sent to a company contact when a collection is created
    'collect' => [
        'subject' => 'Your personalised collection link is ready - :company',
        'eyebrow' => 'Blood collection',
        'heading' => 'Your co-branded link is ready',
        'greeting' => 'Hello,',
        'greeting_name' => 'Hello :name,',
        'body' => 'A blood collection has just been scheduled for :company. Here is your unique co-branded link: share it with your employees so they can book an appointment and discover their donation eligibility.',
        'recap_date' => 'Date',
        'recap_place' => 'Location',
        'cta' => 'View the collection page',
        'link_fallback' => 'If the button does not work, copy this link into your browser:',
    ],

    // Reminder mail sent when a visitor becomes eligible to donate again
    'reminder' => [
        'subject' => 'Good news: you can donate blood again',
        'eyebrow' => 'Blood donation',
        'heading' => 'You can donate again!',
        'greeting' => 'Hello,',
        'body' => 'You left us your address because you were not yet eligible to donate blood. Good news: since :date, you can donate blood again.',
        'cta_intro' => 'Find out how and where to give blood on the HUG website:',
        'cta' => 'Give my blood',
        'outro' => 'Thank you for your generosity: every donation can save up to three lives.',
    ],
];
