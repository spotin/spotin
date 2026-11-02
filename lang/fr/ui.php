<?php

declare(strict_types=1);

return [
    'common' => [
        'leave' => [
            'title' => 'Vous êtes sur le point de quitter :app',
            'warning' => "Ce lien mène à :website, un site web externe à :app, qui n'est pas responsable de son contenu. Continuez seulement si vous lui faites confiance, et soyez prudent avant de donner des informations personnelles.",
            'continue' => 'Continuer vers le site web',
        ],
        'scanned' => ':app ajoute des informations numériques à notre monde physique à l\'aide de « spots ». Partagez des spots avec vos amis et découvrez de nouveaux lieux et de nouvelles choses !',
        'theme' => 'Changer de thème',
        'locale' => [
            'label' => 'Changer de langue',
            'options' => [
                'en' => 'English (Anglais)',
                'fr' => 'Français',
            ],
        ],
        'version' => 'Version :version',
        'account' => 'Menu du compte',
        'filters' => [
            'search' => 'Rechercher',
            'reset' => 'Réinitialiser les filtres',
        ],
        'breadcrumbs' => "Fil d'Ariane",
        'actions' => [
            'label' => 'Actions',
            'like' => "J'aime",
            'report' => 'Signaler',
            'share' => 'Partager',
            'cancel' => 'Annuler',
            'close' => 'Fermer',
            'see_all' => 'Tout voir',
        ],
        'pagination' => [
            'page' => 'Page :current sur :last',
        ],
        'steps' => [
            'previous' => 'Précédent',
            'next' => 'Continuer',
            'position' => 'Étape :current sur :total',
        ],
        'map' => [
            'label' => 'Carte',
            'hint' => "Cliquez sur la carte ou déplacez le marqueur pour choisir l'emplacement.",
            'spot_link' => 'Voir le spot',
            'controls' => [
                'zoom_in' => 'Zoomer',
                'zoom_out' => 'Dézoomer',
                'reset_bearing' => 'Réorienter vers le nord',
                'recenter' => 'Centrer sur le spot',
                'recenter_spots' => 'Afficher tous les spots',
                'fullscreen_enter' => 'Afficher en plein écran',
                'fullscreen_exit' => 'Quitter le plein écran',
                'locate' => 'Afficher ma position',
                'locate_unavailable' => 'Position non disponible',
                'marker' => 'Marqueur de la carte',
                'attribution' => 'Afficher ou masquer les sources de la carte',
                'scroll_windows' => 'Utilisez Ctrl + molette pour zoomer sur la carte',
                'scroll_mac' => 'Utilisez ⌘ + molette pour zoomer sur la carte',
                'two_fingers' => 'Utilisez deux doigts pour déplacer la carte',
            ],
            'fields' => [
                'latitude' => [
                    'label' => 'Latitude',
                    'placeholder' => 'Saisissez la latitude, entre -90 et 90',
                ],
                'longitude' => [
                    'label' => 'Longitude',
                    'placeholder' => 'Saisissez la longitude, entre -180 et 180',
                ],
            ],
        ],
        'markdown_editor' => [
            'toolbar' => 'Mise en forme',
            'hint' => 'Vous pouvez mettre en forme le texte avec Markdown.',
            'block' => [
                'label' => 'Type de texte',
                'paragraph' => 'Paragraphe',
                'heading' => 'Titre :level',
            ],
            'commands' => [
                'bold' => 'Gras',
                'italic' => 'Italique',
                'strike' => 'Barré',
                'code' => 'Code',
                'bullet_list' => 'Liste à puces',
                'ordered_list' => 'Liste numérotée',
                'blockquote' => 'Citation',
                'code_block' => 'Bloc de code',
                'horizontal_rule' => 'Ligne horizontale',
                'link' => 'Lien',
                'image' => 'Image',
            ],
            'prompts' => [
                'link' => 'Adresse du lien (laissez vide pour retirer le lien) :',
                'image' => "Adresse de l'image :",
                'image_alt' => "Description de l'image, pour les personnes qui ne peuvent pas la voir :",
            ],
            'markdown' => 'Afficher le code Markdown',
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
                    'password_confirmation' => [
                        'label' => 'Confirmation du mot de passe',
                        'placeholder' => 'Confirmez votre mot de passe',
                    ],
                    'remember' => [
                        'label' => 'Rester connecté sur cet appareil',
                    ],
                    'current_password' => [
                        'label' => 'Mot de passe actuel',
                        'placeholder' => 'Saisissez votre mot de passe actuel',
                    ],
                    'new_password' => [
                        'label' => 'Nouveau mot de passe',
                        'placeholder' => 'Saisissez votre nouveau mot de passe',
                    ],
                    'new_password_confirmation' => [
                        'label' => 'Confirmer le nouveau mot de passe',
                        'placeholder' => 'Confirmez votre nouveau mot de passe',
                    ],
                ],
            ],
        ],
        'feedback' => [
            'verification_link_sent' => 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.',
        ],
        'register' => [
            'title' => 'Inscription',
            'description' => 'Créez un compte',
            'invitation' => 'Vous créez un compte pour rejoindre :organization. Votre invitation a été envoyée à cette adresse e-mail, elle ne peut donc pas être modifiée.',
            'steps' => [
                'information' => [
                    'title' => 'Information',
                    'audience' => "Spot in® est conçu pour les administrations publiques et les organisations qui gèrent des spots. Il n'est pas destiné à un usage personnel.",
                    'membership' => "L'inscription seule ne permet pas de créer des spots : une organisation doit d'abord vous inviter à la rejoindre. Seuls les membres d'une organisation peuvent créer ses spots.",
                    'discover' => "Vous n'avez pas besoin de compte pour découvrir les spots.",
                ],
                'account' => [
                    'title' => 'Compte',
                ],
            ],
            'form' => [
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
                'options' => 'Options de connexion',
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
                'actions' => [
                    'submit' => 'Envoyer le lien de réinitialisation',
                ],
            ],
        ],
        'reset_password' => [
            'title' => 'Réinitialiser votre mot de passe',
            'description' => 'Choisissez un nouveau mot de passe pour votre compte.',
            'form' => [
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
            ],
        ],
        'password_confirmation' => [
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
        'title' => 'Bienvenue sur Spot in®',
        'description' => "Découvrez les lieux et les points d'intérêt près de chez vous.",
        'introduction' => "Les « spots » représentent des lieux ou des points d'intérêt de notre monde physique. Découvrez les spots près de chez vous et trouvez de nouveaux endroits à visiter !",
        'map' => 'Carte de tous les spots',
        'explore_spots' => 'Explorer les spots',
        'explore_organizations' => 'Parcourir les organisations',
        'dashboard' => 'Aller à mon tableau de bord',
    ],
    'invitations' => [
        'common' => [
            'alert' => [
                'message' => 'Vous avez :count invitation en attente.|Vous avez :count invitations en attente.',
                'action' => 'Voir les invitations',
            ],
        ],
        'feedback' => [
            'invitation_accepted' => "Vous avez rejoint l'organisation.",
            'invitation_declined' => "L'invitation a été refusée.",
            'invalid_invitation' => 'Cette invitation est invalide, a expiré ou a été envoyée à une autre adresse e-mail.',
        ],
        'index' => [
            'title' => 'Invitations en attente',
            'description' => 'Les invitations que vous avez reçues pour rejoindre une organisation.',
            'table' => [
                'empty' => "Vous n'avez aucune invitation en attente.",
                'invited_by' => 'Invitation envoyée par :name',
                'columns' => [
                    'organization' => 'Organisation',
                    'role' => 'Rôle',
                    'actions' => 'Actions',
                ],
                'actions' => [
                    'accept' => 'Accepter',
                    'decline' => [
                        'label' => 'Refuser',
                        'title' => "Refuser l'invitation",
                        'warning' => "Vous ne rejoindrez pas cette organisation. Un responsable devra vous inviter à nouveau si vous changez d'avis.",
                        'actions' => [
                            'submit' => "Refuser l'invitation",
                        ],
                    ],
                ],
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
    'organizations' => [
        'common' => [
            'forms' => [
                'fields' => [
                    'name' => [
                        'label' => 'Nom',
                        'placeholder' => "Saisissez le nom de l'organisation",
                    ],
                    'role' => [
                        'label' => 'Rôle',
                        'placeholder' => 'Sélectionnez le rôle',
                        'options' => [
                            'responsible' => 'Responsable',
                            'member' => 'Membre',
                        ],
                    ],
                ],
            ],
        ],
        'feedback' => [
            'organization_created' => "L'organisation a été créée.",
            'organization_updated' => "L'organisation a été mise à jour.",
            'organization_deleted' => "L'organisation a été supprimée.",
        ],
        'index' => [
            'create' => 'Nouvelle organisation',
            'title' => 'Organisations',
            'description' => 'Découvrez les organisations de la plateforme.',
            'empty' => 'Aucune organisation à afficher.',
            'count' => ':count organisation|:count organisations',
            'no_result' => 'Aucune organisation ne correspond à vos filtres.',
            'filter' => [
                'search' => 'Rechercher une organisation',
                'label' => 'Filtrer les organisations',
                'mine' => 'Uniquement les organisations dont je fais partie',
            ],
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
        'show' => [
            'scanned' => 'Vous venez de scanner une organisation !',
            'map' => "Spots de l'organisation",
            'description' => "Les spots, les membres et les invitations de l'organisation.",
            'actions' => [
                'edit' => 'Modifier',
                'delete' => 'Supprimer',
            ],
            'fields' => [
                'created' => 'Créée le',
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
                    'summary' => ':count invitation|:count invitations',
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
        'invitations' => [
            'common' => [
                'mail' => [
                    'subject' => 'Invitation à rejoindre :organization',
                    'line' => 'Vous avez reçu une invitation à rejoindre :organization en tant que :role.',
                    'action' => "Voir l'invitation",
                    'expires' => 'Cette invitation expire le :date.',
                    'ignore' => "Si vous n'attendiez pas cette invitation, vous pouvez ignorer cet e-mail.",
                ],
            ],
            'feedback' => [
                'invitation_sent' => "L'invitation a été envoyée.",
                'invitation_revoked' => "L'invitation a été révoquée.",
                'already_member' => "Cette personne fait déjà partie de l'organisation.",
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
                        'revoke' => [
                            'label' => 'Révoquer',
                            'title' => "Révoquer l'invitation",
                            'warning' => "Cette action révoque définitivement l'invitation. La personne ne pourra plus l'accepter.",
                            'actions' => [
                                'submit' => "Révoquer l'invitation",
                            ],
                        ],
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
            ],
        ],
        'members' => [
            'feedback' => [
                'member_role_updated' => 'Le rôle a été mis à jour.',
                'member_removed' => 'Le membre a été retiré.',
                'not_a_member' => "Cette personne ne fait pas partie de l'organisation.",
                'last_responsible' => 'Une organisation doit conserver au moins un responsable.',
                'organization_left' => "Vous avez quitté l'organisation.",
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
                        'leave' => [
                            'label' => 'Quitter',
                            'title' => "Quitter l'organisation",
                            'warning' => "Vous perdrez l'accès à cette organisation. Un responsable devra vous inviter à nouveau si vous souhaitez revenir.",
                            'confirm' => "Quitter l'organisation",
                            'last_responsible' => 'Vous êtes le seul responsable de cette organisation. Nommez un autre membre responsable avant de pouvoir la quitter.',
                        ],
                        'remove' => [
                            'label' => 'Retirer',
                            'title' => 'Retirer le membre',
                            'warning' => "Cette personne perdra l'accès à l'organisation. Un responsable devra l'inviter de nouveau si elle souhaite revenir.",
                            'actions' => [
                                'submit' => 'Retirer le membre',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'spots' => [
            'feedback' => [
                'spot_created' => 'Le spot a été créé.',
            ],
            'create' => [
                'title' => 'Nouveau spot',
                'description' => 'Créez un nouveau spot.',
                'introduction' => "Vous êtes sur le point de créer un nouveau spot dans l'organisation :organization.",
                'form' => [
                    'actions' => [
                        'submit' => 'Créer le spot',
                    ],
                ],
            ],
        ],
    ],
    'settings' => [
        'title' => 'Paramètres',
        'description' => 'Gérez votre compte.',
        'developer' => [
            'common' => [
                'forms' => [
                    'fields' => [
                        'name' => [
                            'label' => 'Nom',
                            'placeholder' => 'Saisissez un nom pour le jeton',
                        ],
                        'abilities' => [
                            'label' => 'Autorisations',
                            'options' => [
                                'organizations_read' => 'Lire les organisations',
                                'organizations_create' => 'Créer des organisations',
                                'organizations_update' => 'Modifier les organisations',
                                'organizations_delete' => 'Supprimer les organisations',
                                'spots_read' => 'Lire les spots',
                                'spots_create' => 'Créer des spots',
                                'spots_update' => 'Modifier les spots',
                                'spots_delete' => 'Supprimer les spots',
                            ],
                        ],
                        'expiry' => [
                            'label' => 'Expiration',
                            'placeholder' => "Sélectionnez l'expiration",
                            'options' => [
                                '30' => '30 jours',
                                '90' => '90 jours',
                                '365' => '1 an',
                                'never' => 'Jamais',
                            ],
                        ],
                    ],
                ],
            ],
            'feedback' => [
                'token_created' => 'Le jeton a été créé. Copiez ce jeton maintenant. Pour des raisons de sécurité, il ne sera plus affiché.',
                'token_revoked' => 'Le jeton a été révoqué.',
            ],
            'index' => [
                'title' => 'Paramètres développeur',
                'description' => "Gérez les jetons d'accès personnels utilisés pour accéder à l'API.",
                'warning' => "Cette section est réservée aux utilisateurs avancés. Les jetons d'accès personnels donnent à une application l'accès à l'API en votre nom : ne créez un jeton que si vous savez ce que vous faites, gardez-le secret et révoquez-le quand vous n'en avez plus besoin.",
                'access_the_api' => "La documentation de l'API est disponible à l'adresse :ui et :document.",
                'your_tokens' => 'Vos jetons',
                'table' => [
                    'empty' => "Vous n'avez encore aucun jeton.",
                    'never_used' => 'Jamais utilisé',
                    'never_expires' => "N'expire jamais",
                    'last_used' => 'Dernière utilisation : :when',
                    'expires' => 'Expire le :date',
                    'expired' => 'Expiré',
                    'columns' => [
                        'name' => 'Nom',
                        'abilities' => 'Autorisations',
                        'actions' => 'Actions',
                    ],
                    'actions' => [
                        'revoke' => [
                            'label' => 'Révoquer',
                            'title' => 'Révoquer le jeton',
                            'warning' => 'Les applications qui utilisent ce jeton perdront immédiatement leur accès. Cette action est irréversible.',
                            'actions' => [
                                'submit' => 'Révoquer le jeton',
                            ],
                        ],
                    ],
                ],
            ],
            'create' => [
                'title' => 'Nouveau jeton',
                'description' => "Un jeton donne à une application l'accès à l'API en votre nom, avec les autorisations que vous choisissez.",
                'form' => [
                    'actions' => [
                        'submit' => 'Créer le jeton',
                    ],
                ],
            ],
        ],
        'profile' => [
            'title' => 'Paramètres du profil',
            'description' => 'Gérez les informations de votre profil.',
            'feedback' => [
                'profile_information_updated' => 'Votre profil a été mis à jour.',
            ],
            'form' => [
                'actions' => [
                    'submit' => 'Enregistrer le profil',
                ],
            ],
        ],
        'security' => [
            'title' => 'Paramètres de sécurité',
            'description' => 'Gérez vos préférences de sécurité.',
            'feedback' => [
                'password_updated' => 'Votre mot de passe a été modifié.',
            ],
            'form' => [
                'actions' => [
                    'submit' => 'Modifier le mot de passe',
                ],
            ],
        ],
    ],
    'spots' => [
        'common' => [
            'unknown_creator' => 'Inconnu',
            'hidden' => 'Masqué',
            'forms' => [
                'fields' => [
                    'name' => [
                        'label' => 'Nom',
                        'placeholder' => 'Saisissez le nom du spot',
                    ],
                    'description' => [
                        'label' => 'Description',
                        'placeholder' => 'Décrivez le spot',
                    ],
                    'website' => [
                        'label' => 'Site web',
                        'placeholder' => 'https://example.com',
                    ],
                    'redirect_to_website' => [
                        'label' => 'Rediriger les visiteurs directement vers le site web',
                        'hint' => "Les visiteurs qui scannent le spot vont directement sur le site web, sans voir sa page. Ne s'applique que si le spot a un site web.",
                        'needs_website' => 'Ajoutez un site web vers lequel rediriger les visiteurs, ou désactivez la redirection.',
                    ],
                    'is_hidden' => [
                        'label' => 'Masquer le spot des listes publiques',
                        'hint' => "Seuls les membres de l'organisation trouvent le spot dans les listes et la recherche. Le spot reste accessible par son lien.",
                    ],
                ],
                'steps' => [
                    'location' => 'Emplacement',
                    'details' => 'Détails',
                    'website' => 'Site web et visibilité',
                ],
            ],
        ],
        'feedback' => [
            'spot_updated' => 'Le spot a été mis à jour.',
            'spot_deleted' => 'Le spot a été supprimé.',
        ],
        'index' => [
            'title' => 'Spots',
            'description' => 'Découvrez les spots de la plateforme.',
            'empty' => 'Aucun spot à afficher.',
            'count' => ':count spot|:count spots',
            'map' => 'Carte des spots',
            'no_result' => 'Aucun spot ne correspond à vos filtres.',
            'filter' => [
                'search' => 'Rechercher un spot',
                'label' => 'Filtrer les spots',
                'mine' => "Uniquement les spots que j'ai créés",
            ],
        ],
        'show' => [
            'scanned' => 'Vous venez de scanner un spot !',
            'description' => 'Les détails du spot.',
            'actions' => [
                'edit' => 'Modifier',
                'delete' => 'Supprimer',
            ],
            'fields' => [
                'organization' => 'Organisation',
                'creator' => 'Créé par',
                'created' => 'Créé le',
                'coordinates' => 'Coordonnées',
            ],
            'visit' => 'Visiter :website',
            'map' => 'Emplacement du spot',
            'notes' => [
                'redirect' => 'Les visiteurs qui scannent ce spot sont redirigés directement vers :website.',
                'hidden' => 'Ce spot est masqué des listes publiques : seuls les membres de :organization le trouvent dans les listes. Il reste accessible par son lien.',
            ],
        ],
        'edit' => [
            'title' => 'Modifier le spot',
            'description' => 'Modifiez ou supprimez un spot.',
            'introduction' => "Vous modifiez le spot :spot de l'organisation :organization.",
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
