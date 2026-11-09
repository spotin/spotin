<?php

declare(strict_types=1);

return [
    'contact' => [
        'subject' => 'Nouveau message de :organization',
        'from' => 'De : :name (:email)',
        'organization' => 'Institution : :organization',
        'reply' => 'Répondez à cet e-mail pour lui répondre.',
    ],
    'invitation' => [
        'subject' => 'Invitation à rejoindre :organization',
        'line' => 'Vous avez reçu une invitation à rejoindre :organization en tant que :role.',
        'action' => "Voir l'invitation",
        'expires' => 'Cette invitation expire le :date.',
        'ignore' => "Si vous n'attendiez pas cette invitation, vous pouvez ignorer cet e-mail.",
    ],
    'reset_password' => [
        'subject' => 'Réinitialisation du mot de passe',
        'line' => 'Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.',
        'action' => 'Réinitialiser le mot de passe',
        'expires' => 'Ce lien de réinitialisation du mot de passe expirera dans :count minutes.',
        'ignore' => "Si vous n'avez pas demandé de réinitialisation de mot de passe, vous pouvez ignorer cet e-mail.",
    ],
    'verify_email' => [
        'subject' => "Vérifier l'adresse e-mail",
        'line' => 'Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse e-mail.',
        'action' => "Vérifier l'adresse e-mail",
        'ignore' => "Si vous n'avez pas créé de compte, vous pouvez ignorer cet e-mail.",
    ],
];
