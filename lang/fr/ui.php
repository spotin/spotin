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
        'fill-the-form' => 'Veuillez remplir le formulaire ci-dessous pour continuer.',
        'account' => 'Menu du compte',
        'filters' => [
            'search' => 'Rechercher',
            'reset' => 'Réinitialiser les filtres',
        ],
        'breadcrumbs' => "Fil d'Ariane",
        'actions' => [
            'cancel' => 'Annuler',
            'close' => 'Fermer',
            'see-all' => 'Tout voir',
        ],
        'pagination' => [
            'page' => 'Page :current sur :last',
        ],
    ],
    'auth' => [
        'common' => [
            'forms' => [
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
                    'password-confirmation' => [
                        'label' => 'Confirmation du mot de passe',
                        'placeholder' => 'Confirmez votre mot de passe',
                    ],
                    'remember' => [
                        'label' => 'Se souvenir de moi',
                    ],
                    'current-password' => [
                        'label' => 'Mot de passe actuel',
                        'placeholder' => 'Saisissez votre mot de passe actuel',
                    ],
                    'new-password' => [
                        'label' => 'Nouveau mot de passe',
                        'placeholder' => 'Saisissez votre nouveau mot de passe',
                    ],
                    'new-password-confirmation' => [
                        'label' => 'Confirmer le nouveau mot de passe',
                        'placeholder' => 'Confirmez votre nouveau mot de passe',
                    ],
                ],
            ],
        ],
        'feedback' => [
            'verification-link-sent' => 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.',
        ],
        'register' => [
            'title' => 'Inscription',
            'description' => 'Créez un compte',
            'invitation' => 'Vous créez un compte pour rejoindre :organization. Votre invitation a été envoyée à cette adresse e-mail, elle ne peut donc pas être modifiée.',
            'form' => [
                'actions' => [
                    'submit' => "S'inscrire",
                ],
            ],
            'already-have-an-account' => 'Vous avez déjà un compte ?',
            'login' => 'Se connecter',
        ],
        'login' => [
            'title' => 'Connexion',
            'description' => 'Accédez à votre compte',
            'form' => [
                'actions' => [
                    'submit' => 'Se connecter',
                ],
            ],
            'forgot-your-password' => 'Mot de passe oublié ?',
            'reset-your-password' => 'Réinitialisez votre mot de passe',
            'dont-have-an-account' => "Vous n'avez pas de compte ?",
            'register' => "S'inscrire",
        ],
        'forgot-password' => [
            'title' => 'Mot de passe oublié ?',
            'description' => 'Demandez un lien de réinitialisation du mot de passe.',
            'form' => [
                'actions' => [
                    'submit' => 'Envoyer le lien de réinitialisation',
                ],
            ],
        ],
        'reset-password' => [
            'title' => 'Réinitialiser votre mot de passe',
            'description' => 'Choisissez un nouveau mot de passe pour votre compte.',
            'form' => [
                'actions' => [
                    'submit' => 'Réinitialiser le mot de passe',
                ],
            ],
        ],
        'verify-email' => [
            'title' => 'Vérifiez votre adresse e-mail',
            'description' => 'Confirmez votre adresse e-mail pour terminer la création du compte.',
            'explanation' => "Un lien de vérification a été envoyé à votre adresse e-mail. Veuillez consulter votre boîte de réception et cliquer sur le lien pour vérifier votre adresse. Vous pourrez ensuite utiliser votre compte. Si vous n'avez pas reçu l'e-mail, vous pouvez demander un nouveau lien de vérification ci-dessous.",
            'form' => [
                'actions' => [
                    'resend' => "Renvoyer l'e-mail de vérification",
                ],
            ],
        ],
        'password-confirmation' => [
            'title' => 'Confirmez votre mot de passe',
            'description' => 'Confirmez votre mot de passe pour continuer.',
            'explanation' => 'Pour des raisons de sécurité, veuillez confirmer votre mot de passe avant de continuer.',
            'form' => [
                'actions' => [
                    'submit' => 'Confirmer le mot de passe',
                ],
            ],
        ],
        'logout' => 'Se déconnecter',
    ],
    'dashboard' => [
        'title' => 'Tableau de bord',
        'description' => 'Vos organisations, vos spots et vos invitations.',
        'greeting' => 'Bonjour, :name !',
        'organizations' => [
            'title' => 'Mes organisations',
            'empty' => "Vous ne faites encore partie d'aucune organisation. Demandez à un responsable d'une organisation de vous inviter.",
        ],
        'spots' => [
            'title' => 'Mes spots',
            'empty' => "Vous n'avez pas encore créé de spot.",
        ],
    ],
    'homepage' => [
        'title' => 'Bienvenue sur Spot in',
        'description' => "Découvrez les lieux et les points d'intérêt près de chez vous.",
        'intro' => "Les spots représentent des lieux ou des points d'intérêt de notre monde physique. Découvrez les spots près de chez vous et trouvez de nouveaux endroits à visiter !",
        'explore-spots' => 'Explorer les spots',
        'explore-organizations' => 'Parcourir les organisations',
        'dashboard' => 'Aller à mon tableau de bord',
        'latest-spots' => 'Derniers spots',
        'latest-spots-empty' => "Aucun spot n'a encore été créé.",
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
        'feedback' => [
            'organization-created' => "L'organisation a été créée.",
            'organization-updated' => "L'organisation a été mise à jour.",
            'organization-deleted' => "L'organisation a été supprimée.",
        ],
        'create' => [
            'title' => 'Nouvelle organisation',
            'description' => 'Créez une nouvelle organisation.',
            'form' => [
                'actions' => [
                    'submit' => "Créer l'organisation",
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
            ],
        ],
        'delete' => [
            'title' => "Supprimer l'organisation",
            'warning' => "Cette action supprime définitivement l'organisation, ses appartenances et ses invitations en attente. Elle est irréversible.",
            'actions' => [
                'submit' => "Supprimer l'organisation",
            ],
        ],
        'index' => [
            'create' => 'Nouvelle organisation',
            'title' => 'Organisations',
            'description' => 'Découvrez les organisations de la plateforme.',
            'empty' => 'Aucune organisation à afficher.',
            'count' => ':count organisation|:count organisations',
            'no-result' => 'Aucune organisation ne correspond à vos filtres.',
            'filter' => [
                'search' => 'Rechercher une organisation',
                'label' => 'Filtrer les organisations',
                'mine' => 'Uniquement les organisations dont je fais partie',
            ],
            'pending-invitations' => 'Vous avez :count invitation en attente.|Vous avez :count invitations en attente.',
            'view-pending-invitations' => 'Voir les invitations',
        ],
        'show' => [
            'description' => "Les spots, les membres et les invitations de l'organisation.",
            'actions' => [
                'edit' => 'Modifier',
                'delete' => 'Supprimer',
            ],
            'sections' => [
                'members' => [
                    'title' => 'Membres',
                    'summary' => ':count membre|:count membres',
                ],
                'spots' => [
                    'title' => 'Spots',
                    'summary' => ':count spot|:count spots',
                ],
                'invitations' => [
                    'title' => 'Invitations',
                    'summary' => ':count invitation en attente|:count invitations en attente',
                ],
            ],
        ],
        'invitations' => [
            'feedback' => [
                'invitation-sent' => "L'invitation a été envoyée.",
                'invitation-revoked' => "L'invitation a été révoquée.",
                'invitation-accepted' => "Vous avez rejoint l'organisation.",
                'invitation-declined' => "L'invitation a été refusée.",
                'invalid-invitation' => 'Cette invitation est invalide, a expiré ou a été envoyée à une autre adresse e-mail.',
                'already-member' => "Cette personne fait déjà partie de l'organisation.",
            ],
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
            ],
            'pending' => [
                'title' => 'Invitations en attente',
                'description' => 'Les invitations que vous avez reçues pour rejoindre une organisation.',
                'table' => [
                    'empty' => "Vous n'avez aucune invitation en attente.",
                    'invited-by' => 'Invitation envoyée par :name',
                    'columns' => [
                        'organization' => 'Organisation',
                        'role' => 'Rôle',
                        'actions' => 'Actions',
                    ],
                    'actions' => [
                        'accept' => 'Accepter',
                        'decline' => 'Refuser',
                    ],
                ],
            ],
            'decline' => [
                'title' => "Refuser l'invitation",
                'warning' => "Vous ne rejoindrez pas cette organisation. Un responsable devra vous inviter à nouveau si vous changez d'avis.",
                'actions' => [
                    'submit' => "Refuser l'invitation",
                ],
            ],
            'revoke' => [
                'title' => "Révoquer l'invitation",
                'warning' => "Cette action révoque définitivement l'invitation. La personne ne pourra plus l'accepter.",
                'actions' => [
                    'submit' => "Révoquer l'invitation",
                ],
            ],
            'show' => [
                'title' => 'Rejoindre une organisation',
                'description' => 'Acceptez votre invitation à rejoindre une organisation.',
                'explanation' => 'Vous avez reçu une invitation à rejoindre :organization en tant que :role.',
                'actions' => [
                    'register' => 'Créer un compte',
                    'login' => "J'ai déjà un compte",
                ],
            ],
        ],
        'members' => [
            'feedback' => [
                'member-role-updated' => 'Le rôle a été mis à jour.',
                'member-removed' => 'Le membre a été retiré.',
                'not-a-member' => "Cette personne ne fait pas partie de l'organisation.",
                'last-responsible' => 'Une organisation doit conserver au moins un responsable.',
                'organization-left' => "Vous avez quitté l'organisation.",
            ],
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
                'last-responsible' => 'Vous êtes le seul responsable de cette organisation. Nommez un autre membre responsable avant de pouvoir la quitter.',
            ],
        ],
    ],
    'settings' => [
        'title' => 'Paramètres',
        'description' => 'Gérez votre compte.',
        'feedback' => [
            'profile-information-updated' => 'Votre profil a été mis à jour.',
            'password-updated' => 'Votre mot de passe a été modifié.',
        ],
        'profile' => [
            'title' => 'Paramètres du profil',
            'description' => 'Gérez les informations de votre profil.',
            'form' => [
                'actions' => [
                    'submit' => 'Enregistrer le profil',
                ],
            ],
        ],
        'security' => [
            'title' => 'Paramètres de sécurité',
            'description' => 'Modifiez votre mot de passe.',
            'form' => [
                'actions' => [
                    'submit' => 'Modifier le mot de passe',
                ],
            ],
        ],
    ],
    'spots' => [
        'index' => [
            'create' => 'Nouveau spot',
            'title' => 'Spots',
            'description' => 'Découvrez les spots de la plateforme.',
            'empty' => 'Aucun spot à afficher.',
            'count' => ':count spot|:count spots',
            'no-result' => 'Aucun spot ne correspond à vos filtres.',
            'filter' => [
                'search' => 'Rechercher un spot',
                'label' => 'Filtrer les spots',
                'mine' => "Uniquement les spots que j'ai créés",
            ],
        ],
        'common' => [
            'unknown-creator' => 'Inconnu',
            'forms' => [
                'fields' => [
                    'name' => [
                        'label' => 'Nom du spot',
                        'placeholder' => 'Saisissez le nom du spot',
                    ],
                    'organization' => [
                        'label' => 'Organisation',
                        'placeholder' => 'Sélectionnez une organisation',
                    ],
                ],
            ],
        ],
        'feedback' => [
            'spot-created' => 'Le spot a été créé.',
            'spot-updated' => 'Le spot a été mis à jour.',
            'spot-deleted' => 'Le spot a été supprimé.',
        ],
        'create' => [
            'title' => 'Nouveau spot',
            'description' => 'Créez un nouveau spot.',
            'no-organization' => "Vous devez faire partie d'une organisation pour créer un spot.",
            'form' => [
                'actions' => [
                    'submit' => 'Créer le spot',
                ],
            ],
        ],
        'show' => [
            'description' => 'Les détails du spot.',
            'actions' => [
                'edit' => 'Modifier',
                'delete' => 'Supprimer',
            ],
            'fields' => [
                'organization' => 'Organisation',
                'creator' => 'Créé par',
                'created' => 'Créé le',
            ],
        ],
        'edit' => [
            'title' => 'Modifier le spot',
            'description' => 'Modifiez ou supprimez un spot.',
            'form' => [
                'actions' => [
                    'submit' => 'Enregistrer',
                ],
            ],
        ],
        'delete' => [
            'title' => 'Supprimer le spot',
            'warning' => 'Cela supprime définitivement le spot. Cette action est irréversible.',
            'actions' => [
                'submit' => 'Supprimer le spot',
            ],
        ],
    ],
];
