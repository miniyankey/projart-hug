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
