<?php

return [
    // Pied de page commun à tous les mails
    'footer' => [
        'signature' => 'Centre de transfusion sanguine - HUG',
        'disclaimer' => 'Vous recevez ce message dans le cadre du programme de dons de sang en entreprise.',
    ],

    // Mail envoyé au contact d'une entreprise quand une collecte est créée
    'collect' => [
        'subject' => 'Votre lien de collecte personnalisé est prêt - :company',
        'eyebrow' => 'Collecte de sang',
        'heading' => 'Votre lien co-brandé est prêt',
        'greeting' => 'Bonjour,',
        'greeting_name' => 'Bonjour :name,',
        'body' => 'Une collecte de sang vient d\'être programmée pour :company. Voici votre lien co-brandé unique : partagez-le à vos collaborateurs pour leur permettre de prendre rendez-vous et de découvrir leur éligibilité au don.',
        'recap_date' => 'Date',
        'recap_place' => 'Lieu',
        'cta' => 'Voir la page de la collecte',
        'link_fallback' => 'Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :',
    ],

    // Notification interne à l'équipe quand un formulaire public est soumis
    'form_submission' => [
        'subject_contact' => 'Nouveau message de contact - :company',
        'subject_collect' => 'Nouvelle demande de collecte - :company',
        'eyebrow' => 'Formulaire',
        'heading_contact' => 'Nouveau message de contact',
        'heading_collect' => 'Nouvelle demande d\'organisation de collecte',
        'intro' => 'Un formulaire vient d\'être soumis sur le site. Voici les informations transmises :',
        'field_company' => 'Entreprise',
        'field_city' => 'Ville',
        'field_name' => 'Contact',
        'field_email' => 'E-mail',
        'field_message' => 'Message',
        'field_dates' => 'Dates souhaitées',
        'field_trophy' => 'Trophée de la générosité',
        'trophy_yes' => 'Souhaite participer',
        'cta' => 'Voir dans le dashboard',
        'reply_hint' => 'Répondez directement à ce mail pour contacter :email.',
    ],

    // Mail de rappel quand un visiteur redevient éligible au don
    'reminder' => [
        'subject' => 'Bonne nouvelle : vous pouvez à nouveau donner votre sang',
        'eyebrow' => 'Don du sang',
        'heading' => 'Vous pouvez à nouveau donner !',
        'greeting' => 'Bonjour,',
        'body' => 'Vous nous aviez laissé votre adresse car vous n\'étiez pas encore éligible au don du sang. Bonne nouvelle : depuis le :date, vous pouvez de nouveau donner votre sang.',
        'cta_intro' => 'Retrouvez comment et où donner votre sang sur le site des HUG :',
        'cta' => 'Donner mon sang',
        'outro' => 'Merci pour votre générosité : chaque don peut sauver jusqu\'à trois vies.',
    ],
];
