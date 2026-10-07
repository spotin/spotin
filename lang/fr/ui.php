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
                    ],
                    'name' => [
                        'label' => 'Nom complet',
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
                    'password_confirmation' => [
                        'label' => 'Confirmation du mot de passe',
                        'placeholder' => 'Confirmez votre mot de passe',
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
                    ],
                    'password' => [
                        'label' => 'Nouveau mot de passe',
                        'placeholder' => 'Saisissez votre nouveau mot de passe',
                    ],
                    'password_confirmation' => [
                        'label' => 'Confirmation du nouveau mot de passe',
                        'placeholder' => 'Confirmez votre nouveau mot de passe',
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
        'password_confirmation' => [
            'title' => 'Confirmez votre mot de passe',
            'description' => 'Confirmez votre mot de passe pour continuer.',
        ],
    ],
    'settings' => [
        'title' => 'Paramètres',
        'profile' => [
            'title' => 'Paramètres du profil',
            'description' => 'Gérez les informations de votre profil.',
            'form' => [
                'fields' => [
                    'username' => [
                        'label' => 'Nom d’utilisateur',
                        'placeholder' => 'Saisissez votre nom d’utilisateur',
                    ],
                    'name' => [
                        'label' => 'Nom complet',
                        'placeholder' => 'Saisissez votre nom complet',
                    ],
                    'email' => [
                        'label' => 'Adresse e-mail',
                        'placeholder' => 'Saisissez votre adresse e-mail',
                    ],
                ],
                'actions' => [
                    'submit' => 'Enregistrer le profil',
                ],
            ],
        ],
        'security' => [
            'title' => 'Paramètres de sécurité',
            'description' => 'Modifiez votre mot de passe.',
            'form' => [
                'fields' => [
                    'current_password' => [
                        'label' => 'Mot de passe actuel',
                        'placeholder' => 'Saisissez votre mot de passe actuel',
                    ],
                    'new_password' => [
                        'label' => 'Nouveau mot de passe',
                        'placeholder' => 'Saisissez votre nouveau mot de passe',
                    ],
                    'confirm_password' => [
                        'label' => 'Confirmer le nouveau mot de passe',
                        'placeholder' => 'Confirmez votre nouveau mot de passe',
                    ],
                ],
                'actions' => [
                    'submit' => 'Modifier le mot de passe',
                ],
            ],
        ],
    ],
    'common' => [
        'logout' => 'Se déconnecter',
        'theme' => 'Changer de thème',
        'locale' => [
            'label' => 'Changer de langue',
            'options' => [
                'en' => 'English (Anglais)',
                'fr' => 'Français',
            ],
        ],
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
