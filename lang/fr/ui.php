<?php

declare(strict_types=1);

return [
    'welcome' => [
        'title' => 'Bienvenue',
        'description' => 'Bienvenue sur notre application',
        'first_paragraph' => 'Texte du premier paragraphe.',
        'second_paragraph' => 'Texte du deuxième paragraphe.',
        'button' => 'Cliquez ici',
    ],
    'auth' => [
        'register' => [
            'title' => 'Inscription',
            'description' => 'Créez un compte',
            'form' => [
                'fields' => [
                    'username' => [
                        'label' => "Nom d'utilisateur",
                        'placeholder' => "Saisissez votre nom d'utilisateur",
                        'hint' => 'Utilisez de 3 à 30 caractères : lettres, chiffres, traits d’union ou traits de soulignement uniquement.',
                    ],
                    'name' => [
                        'label' => 'Nom complet',
                        'placeholder' => 'Saisissez votre nom complet',
                        'hint' => 'Utilisez de 2 à 50 caractères : lettres, espaces, apostrophes ou traits d’union uniquement.',
                    ],
                    'email' => [
                        'label' => 'Adresse e-mail',
                        'placeholder' => 'Saisissez votre adresse e-mail',
                        'hint' => 'Saisissez une adresse e-mail valide.',
                    ],
                    'password' => [
                        'label' => 'Mot de passe',
                        'placeholder' => 'Saisissez votre mot de passe',
                        'hint' => 'Utilisez de 8 à 128 caractères.',
                    ],
                    'confirm_password' => [
                        'label' => 'Confirmation du mot de passe',
                        'placeholder' => 'Confirmez votre mot de passe',
                        'hint' => 'Utilisez de 8 à 128 caractères.',
                    ],
                ],
                'actions' => [
                    'submit' => "S'inscrire",
                ],
            ],
            'already_have_an_account' => 'Vous avez déjà un compte ?',
            'login' => 'Se connecter',
        ],
        'login' => [
            'title' => 'Connexion',
            'description' => 'Accédez à votre compte',
            'form' => [
                'fields' => [
                    'username_or_email' => [
                        'label' => "Nom d'utilisateur ou adresse e-mail",
                        'placeholder' => "Saisissez votre nom d'utilisateur ou votre adresse e-mail",
                        'hint' => "Saisissez votre nom d'utilisateur ou votre adresse e-mail.",
                    ],
                    'password' => [
                        'label' => 'Mot de passe',
                        'placeholder' => 'Saisissez votre mot de passe',
                        'hint' => 'Saisissez votre mot de passe.',
                    ],
                ],
                'actions' => [
                    'submit' => 'Se connecter',
                ],
            ],
            'remember_me' => 'Se souvenir de moi',
            'forgot_your_password' => 'Mot de passe oublié ?',
            'reset_your_password' => 'Réinitialisez votre mot de passe',
            'dont_have_an_account' => "Vous n'avez pas de compte ?",
            'register' => "S'inscrire",
        ],
        'forgot_password' => [
            'title' => 'Mot de passe oublié ?',
            'description' => 'Demandez un lien de réinitialisation du mot de passe.',
            'form' => [
                'fields' => [
                    'email' => [
                        'label' => 'Adresse e-mail',
                        'placeholder' => 'Saisissez votre adresse e-mail',
                        'hint' => 'Saisissez une adresse e-mail valide.',
                    ],
                ],
                'actions' => [
                    'submit' => 'Envoyer le lien de réinitialisation',
                ],
            ],
        ],
        'reset_password' => [
            'title' => 'Réinitialiser votre mot de passe',
            'description' => 'Choisissez un nouveau mot de passe pour votre compte.',
            'form' => [
                'fields' => [
                    'email' => [
                        'label' => 'Adresse e-mail',
                        'placeholder' => 'Saisissez votre adresse e-mail',
                        'hint' => 'Saisissez une adresse e-mail valide.',
                    ],
                    'password' => [
                        'label' => 'Nouveau mot de passe',
                        'placeholder' => 'Saisissez votre nouveau mot de passe',
                        'hint' => 'Utilisez de 8 à 128 caractères.',
                    ],
                    'confirm_password' => [
                        'label' => 'Confirmation du nouveau mot de passe',
                        'placeholder' => 'Confirmez votre nouveau mot de passe',
                        'hint' => 'Utilisez de 8 à 128 caractères.',
                    ],
                ],
                'actions' => [
                    'submit' => 'Réinitialiser le mot de passe',
                ],
            ],
            'already_have_an_account' => 'Vous avez déjà un compte ?',
            'login' => 'Se connecter',
        ],
        'verify_email' => [
            'title' => 'Vérifiez votre adresse e-mail',
            'description' => 'Confirmez votre adresse e-mail pour terminer la création du compte.',
            'explanation' => 'Un lien de vérification a été envoyé à votre adresse e-mail. Veuillez consulter votre boîte de réception et cliquer sur le lien pour vérifier votre adresse. Vous pourrez ensuite utiliser votre compte. Si vous n’avez pas reçu l’e-mail, vous pouvez demander un nouveau lien de vérification ci-dessous.',
            'success' => 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.',
            'form' => [
                'actions' => [
                    'resend' => 'Renvoyer l’e-mail de vérification',
                ],
            ],
        ],
        'confirm_password' => [
            'title' => 'Confirmez votre mot de passe',
            'description' => 'Confirmez votre mot de passe pour continuer.',
        ],
    ],
    'common' => [
        'logout' => 'Se déconnecter',
        'version' => 'Version :version',
        'fill_the_form' => 'Veuillez remplir le formulaire ci-dessous pour continuer.',
        'required_fields' => 'Tous les champs marqués d’un astérisque (*) sont obligatoires.',
    ],
    'dashboard' => [
        'title' => 'Tableau de bord',
        'description' => 'Bienvenue sur votre tableau de bord',
        'first_paragraph' => 'Ceci est le premier paragraphe du tableau de bord.',
        'second_paragraph' => 'Ceci est le deuxième paragraphe du tableau de bord.',
    ],
];
