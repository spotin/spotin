<?php

declare(strict_types=1);

return [
    'welcome' => [
        'title' => 'Bienvenue',
        'first_paragraph' => 'Premier paragraphe.',
        'second_paragraph' => 'Deuxième paragraphe.',
        'button' => 'Cliquez ici',
    ],
    'auth' => [
        'sign_up' => [
            'title' => 'Inscription',
            'description' => 'Créez un compte',
            'form' => [
                'fields' => [
                    'username' => [
                        'label' => "Nom d'utilisateur",
                        'placeholder' => "Saisissez votre nom d'utilisateur",
                    ],
                    'name' => [
                        'label' => 'Nom',
                        'placeholder' => 'Saisissez votre nom complet',
                    ],
                    'email' => [
                        'label' => 'Adresse e-mail',
                        'placeholder' => 'Saisissez votre adresse e-mail',
                    ],
                    'password' => [
                        'label' => 'Mot de passe',
                        'placeholder' => 'Saisissez votre mot de passe',
                    ],
                    'confirm_password' => [
                        'label' => 'Confirmation du mot de passe',
                        'placeholder' => 'Confirmez votre mot de passe',
                    ],
                ],
                'actions' => [
                    'submit' => "S'inscrire",
                ],
            ],
            'already_have_an_account' => 'Vous avez déjà un compte ?',
            'sign_in' => 'Se connecter',
        ],
        'sign_in' => [
            'title' => 'Connexion',
            'description' => 'Accédez à votre compte',
            'form' => [
                'fields' => [
                    'email' => [
                        'label' => 'Adresse e-mail',
                        'placeholder' => 'Saisissez votre adresse e-mail',
                    ],
                    'password' => [
                        'label' => 'Mot de passe',
                        'placeholder' => 'Saisissez votre mot de passe',
                    ],
                ],
                'actions' => [
                    'submit' => 'Se connecter',
                ],
            ],
            'remember_me' => 'Se souvenir de moi',
            'forgot_your_password' => 'Mot de passe oublié ?',
            'dont_have_an_account' => "Vous n'avez pas de compte ?",
            'sign_up' => "S'inscrire",
        ],
    ],
    'common' => [
        'version' => 'Version :version',
        'required_fields' => 'Tous les champs marqués d’un astérisque (*) sont obligatoires.',
    ],
];
