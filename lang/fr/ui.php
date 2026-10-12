<?php

declare(strict_types=1);

return [
    'common' => [
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
        'breadcrumbs' => "Fil d'Ariane",
        'actions' => [
            'submit' => 'Valider',
            'cancel' => 'Annuler',
            'close' => 'Fermer',
        ],
        'pagination' => [
            'page' => 'Page :current sur :last',
        ],
    ],
    'admin' => [
        'title' => 'Administration',
        'description' => 'Gérez la plateforme.',
        'organizations' => [
            'index' => [
                'title' => 'Organisations',
                'description' => 'Gérez toutes les organisations.',
                'create' => 'Nouvelle organisation',
                'table' => [
                    'empty' => "Il n'y a pas encore d'organisation.",
                    'columns' => [
                        'name' => 'Nom',
                        'members' => 'Membres',
                        'actions' => 'Actions',
                    ],
                    'actions' => [
                        'show' => 'Voir',
                        'edit' => 'Modifier',
                        'delete' => 'Supprimer',
                    ],
                ],
            ],
            'create' => [
                'title' => 'Nouvelle organisation',
                'description' => 'Créez une nouvelle organisation.',
                'form' => [
                    'actions' => [
                        'submit' => "Créer l'organisation",
                    ],
                    'feedback' => [
                        'success' => "L'organisation a été créée.",
                    ],
                ],
            ],
            'edit' => [
                'title' => "Modifier l'organisation",
                'description' => 'Modifiez ou supprimez une organisation.',
                'form' => [
                    'actions' => [
                        'submit' => 'Enregistrer',
                    ],
                    'feedback' => [
                        'success' => "L'organisation a été mise à jour.",
                    ],
                ],
            ],
            'delete' => [
                'title' => "Supprimer l'organisation",
                'warning' => "Cette action supprime définitivement l'organisation, ses appartenances et ses invitations en attente. Elle est irréversible.",
                'actions' => [
                    'submit' => "Supprimer l'organisation",
                ],
                'feedback' => [
                    'success' => "L'organisation a été supprimée.",
                ],
            ],
        ],
    ],
    'auth' => [
        'register' => [
            'title' => 'Inscription',
            'description' => 'Créez un compte',
            'form' => [
                'fields' => [
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
                    'email' => [
                        'label' => 'Adresse e-mail',
                        'placeholder' => 'Saisissez votre adresse e-mail',
                    ],
                    'password' => [
                        'label' => 'Mot de passe',
                        'placeholder' => 'Saisissez votre mot de passe',
                    ],
                    'remember' => [
                        'label' => 'Se souvenir de moi',
                    ],
                ],
                'actions' => [
                    'submit' => 'Se connecter',
                ],
            ],
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
        ],
        'verify_email' => [
            'title' => 'Vérifiez votre adresse e-mail',
            'description' => 'Confirmez votre adresse e-mail pour terminer la création du compte.',
            'explanation' => "Un lien de vérification a été envoyé à votre adresse e-mail. Veuillez consulter votre boîte de réception et cliquer sur le lien pour vérifier votre adresse. Vous pourrez ensuite utiliser votre compte. Si vous n'avez pas reçu l'e-mail, vous pouvez demander un nouveau lien de vérification ci-dessous.",
            'form' => [
                'actions' => [
                    'resend' => "Renvoyer l'e-mail de vérification",
                ],
                'feedback' => [
                    'success' => 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.',
                ],
            ],
        ],
        'password_confirmation' => [
            'title' => 'Confirmez votre mot de passe',
            'description' => 'Confirmez votre mot de passe pour continuer.',
            'explanation' => 'Pour des raisons de sécurité, veuillez confirmer votre mot de passe avant de continuer.',
            'form' => [
                'fields' => [
                    'password' => [
                        'label' => 'Mot de passe',
                        'placeholder' => 'Saisissez votre mot de passe',
                    ],
                ],
                'actions' => [
                    'submit' => 'Confirmer le mot de passe',
                ],
            ],
        ],
        'logout' => 'Se déconnecter',
    ],
    'organizations' => [
        'common' => [
            'forms' => [
                'fields' => [
                    'name' => [
                        'label' => "Nom de l'organisation",
                        'placeholder' => "Saisissez le nom de l'organisation",
                    ],
                    'role' => [
                        'label' => 'Rôle',
                        'options' => [
                            'responsible' => 'Responsable',
                            'member' => 'Membre',
                        ],
                    ],
                ],
            ],
        ],
        'index' => [
            'title' => 'Mes organisations',
            'description' => 'Les organisations dont vous faites partie.',
            'empty' => "Vous ne faites encore partie d'aucune organisation. Demandez à un responsable d'une organisation de vous inviter.",
        ],
        'show' => [
            'description' => "Les membres et les invitations de l'organisation.",
            'actions' => [
                'edit' => 'Modifier',
                'delete' => 'Supprimer',
            ],
            'sections' => [
                'members' => [
                    'title' => 'Membres',
                    'summary' => ':count membre|:count membres',
                ],
                'invitations' => [
                    'title' => 'Invitations',
                    'summary' => ':count invitation en attente|:count invitations en attente',
                ],
            ],
        ],
        'invitations' => [
            'index' => [
                'title' => 'Invitations',
                'description' => "Les invitations en attente pour rejoindre l'organisation.",
                'invite' => "Inviter quelqu'un",
                'table' => [
                    'empty' => "Il n'y a aucune invitation en attente.",
                    'expired' => 'Expirée',
                    'columns' => [
                        'email' => 'Adresse e-mail',
                        'role' => 'Rôle',
                        'expires' => 'Expire le',
                        'actions' => 'Actions',
                    ],
                    'actions' => [
                        'resend' => 'Renvoyer',
                        'revoke' => 'Révoquer',
                    ],
                ],
            ],
            'create' => [
                'title' => "Inviter quelqu'un",
                'description' => "Invitez quelqu'un à rejoindre l'organisation.",
                'fields' => [
                    'email' => [
                        'label' => 'Adresse e-mail',
                        'placeholder' => "Saisissez l'adresse e-mail de la personne à inviter",
                    ],
                ],
                'actions' => [
                    'submit' => "Envoyer l'invitation",
                ],
                'mail' => [
                    'subject' => 'Invitation à rejoindre :organization',
                    'line' => 'Vous avez reçu une invitation à rejoindre :organization en tant que :role.',
                    'action' => "Voir l'invitation",
                    'expires' => 'Cette invitation expire le :date.',
                    'ignore' => "Si vous n'attendiez pas cette invitation, vous pouvez ignorer cet e-mail.",
                ],
                'feedback' => [
                    'invitation-sent' => "L'invitation a été envoyée.",
                ],
            ],
            'revoke' => [
                'title' => "Révoquer l'invitation",
                'warning' => "Cette action révoque définitivement l'invitation. La personne ne pourra plus l'accepter.",
                'actions' => [
                    'submit' => "Révoquer l'invitation",
                ],
                'feedback' => [
                    'success' => "L'invitation a été révoquée.",
                ],
            ],
            'show' => [
                'title' => 'Rejoindre une organisation',
                'description' => 'Acceptez votre invitation à rejoindre une organisation.',
                'explanation' => 'Vous avez reçu une invitation à rejoindre :organization en tant que :role.',
                'actions' => [
                    'accept' => "Accepter l'invitation",
                ],
                'feedback' => [
                    'success' => "Vous avez rejoint l'organisation.",
                    'invalid' => 'Cette invitation est invalide, a expiré ou a été envoyée à une autre adresse e-mail.',
                    'already_member' => "Cette personne fait déjà partie de l'organisation.",
                ],
            ],
        ],
        'members' => [
            'index' => [
                'title' => 'Membres',
                'description' => "Les membres de l'organisation et leurs rôles.",
                'table' => [
                    'empty' => "Il n'y a pas encore de membre.",
                    'you' => 'Vous',
                    'columns' => [
                        'name' => 'Nom',
                        'role' => 'Rôle',
                        'actions' => 'Actions',
                    ],
                    'actions' => [
                        'invite' => "Inviter quelqu'un",
                        'save' => 'Enregistrer',
                        'leave' => 'Quitter',
                        'remove' => 'Retirer',
                    ],
                    'feedback' => [
                        'member-role-updated' => 'Le rôle a été mis à jour.',
                        'member-removed' => 'Le membre a été retiré.',
                        'not_a_member' => "Cette personne ne fait pas partie de l'organisation.",
                        'last_responsible' => 'Une organisation doit conserver au moins un responsable.',
                    ],
                ],
            ],
            'remove' => [
                'title' => 'Retirer le membre',
                'warning' => "Cette personne perdra l'accès à l'organisation. Un responsable devra l'inviter de nouveau si elle souhaite revenir.",
                'actions' => [
                    'submit' => 'Retirer le membre',
                ],
            ],
            'leave' => [
                'title' => "Quitter l'organisation",
                'warning' => "Vous perdrez l'accès à cette organisation. Un responsable devra vous inviter à nouveau si vous souhaitez revenir.",
                'confirm' => "Quitter l'organisation",
                'last_responsible' => 'Vous êtes le seul responsable de cette organisation. Nommez un autre membre responsable avant de pouvoir la quitter.',
                'feedback' => [
                    'success' => "Vous avez quitté l'organisation.",
                ],
            ],
        ],
    ],
    'settings' => [
        'title' => 'Paramètres',
        'description' => 'Gérez votre compte.',
        'profile' => [
            'title' => 'Paramètres du profil',
            'description' => 'Gérez les informations de votre profil.',
            'form' => [
                'fields' => [
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
                'feedback' => [
                    'success' => 'Votre profil a été mis à jour.',
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
                'feedback' => [
                    'success' => 'Votre mot de passe a été modifié.',
                ],
            ],
        ],
    ],
    'dashboard' => [
        'title' => 'Tableau de bord',
        'first_paragraph' => 'Ceci est le premier paragraphe du tableau de bord.',
        'second_paragraph' => 'Ceci est le deuxième paragraphe du tableau de bord.',
    ],
    'welcome' => [
        'title' => 'Bienvenue',
        'description' => 'Bienvenue sur notre application',
        'first_paragraph' => 'Texte du premier paragraphe.',
        'second_paragraph' => 'Texte du deuxième paragraphe.',
        'button' => 'Cliquez ici',
    ],
];
