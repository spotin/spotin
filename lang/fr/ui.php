<?php

declare(strict_types=1);

return [
    'common' => [
        'qr_code' => [
            'label' => 'Code QR',
            'download_svg' => 'Télécharger en SVG',
            'download_png' => 'Télécharger en PNG',
            'scan_link' => 'Lien de scan',
            'open' => 'Ouvrir le lien de scan',
            'hint' => 'Ouvre la page comme un scan.',
            'copy' => 'Copier',
            'copied' => 'Copié',
        ],
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
            'hint' => "Cliquez sur la carte ou déplacez le marqueur pour choisir l'emplacement. Laissez les coordonnées vides si le spot n'a pas d'emplacement.",
            'incomplete' => 'Saisissez la latitude et la longitude, ou laissez-les vides.',
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
            'invitation' => 'Vous créez un compte pour rejoindre :organization, que vous rejoindrez dès votre compte créé. Votre invitation a été envoyée à cette adresse e-mail, elle ne peut donc pas être modifiée.',
            'invitation_only' => [
                'title' => 'Les comptes sont créés sur invitation',
                'text' => "Pour créer un compte, suivez le lien de l'invitation qu'une organisation vous a envoyée par e-mail. Vous n'avez pas besoin de compte pour découvrir les spots.",
                'contact' => 'Votre institution souhaite utiliser Spot in® ? Contactez-nous.',
                'required' => "Un compte ne peut être créé qu'en suivant une invitation, avec l'adresse e-mail à laquelle elle a été envoyée.",
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
            'reset_your_password' => 'Réinitialisez votre mot de passe.',
            'dont_have_an_account' => "Vous n'avez pas de compte ?",
            'contact' => 'Contactez-nous et nous pourrons vous inviter sur la plateforme.',
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
    'contact' => [
        'title' => 'Nous contacter',
        'description' => 'Votre institution souhaite utiliser Spot in® ? Écrivez-nous, nous vous répondons rapidement.',
        'fields' => [
            'name' => [
                'label' => 'Nom',
                'placeholder' => 'Saisissez votre nom',
            ],
            'email' => [
                'label' => 'Adresse e-mail',
                'placeholder' => 'Saisissez votre adresse e-mail',
            ],
            'organization' => [
                'label' => 'Institution',
                'placeholder' => 'Saisissez le nom de votre institution',
            ],
            'message' => [
                'label' => 'Message',
                'placeholder' => 'Dites-nous en plus sur vos besoins',
            ],
            'website' => [
                'label' => 'Laissez ce champ vide',
            ],
        ],
        'actions' => [
            'submit' => 'Envoyer',
        ],
        'feedback' => [
            'sent' => 'Merci pour votre message ! Nous vous répondons rapidement.',
        ],
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
        'title' => 'Des codes QR qui ne périment jamais',
        'description' => 'La plateforme des institutions publiques pour créer et gérer leurs codes QR.',
        'contact' => 'Nous contacter',
        'audience' => 'Pour les communes, les régions, les musées, les bibliothèques et les offices du tourisme.',
        'map' => 'Carte de tous les spots',
        'dashboard' => 'Aller à mon tableau de bord',
        'call_to_action' => [
            'title' => 'Votre institution est intéressée ?',
            'text' => 'Parlez-nous de vos besoins : nous créons votre organisation et vous accompagnons dans vos premiers pas.',
        ],
        'advantages' => [
            'title' => 'Pourquoi Spot in®',
            'lasting' => [
                'title' => 'Imprimés une fois, valables pour toujours',
                'text' => 'Vos codes QR mènent à Spot in®, et vous changez leur destination à tout moment, sans rien réimprimer. Fini les codes QR qui mènent à des pages disparues.',
            ],
            'content' => [
                'title' => 'Un contenu que vous maîtrisez',
                'text' => 'Chaque code QR représente un lieu ou une entité, avec sa propre page, ou redirige vers le site web de votre choix.',
            ],
            'governance' => [
                'title' => 'Un cadre clair',
                'text' => 'Vous décidez qui crée et gère les codes QR de votre organisation, grâce aux rôles de responsable et de membre.',
            ],
        ],
        'workflow' => [
            'title' => 'Comment ça marche',
            'organization' => [
                'title' => 'Nous créons votre organisation',
                'text' => "Vous nous contactez, nous ouvrons l'espace de votre institution et vous en devenez responsable.",
            ],
            'members' => [
                'title' => 'Vous invitez vos collègues',
                'text' => 'Comme responsables ou comme membres.',
            ],
            'spots' => [
                'title' => 'Vous créez vos spots',
                'text' => 'Un lieu, une entité ou une redirection vers votre site, ou un code à mettre en place sur site par la première personne qui le scanne.',
            ],
            'print' => [
                'title' => 'Vous imprimez les codes QR',
                'text' => 'En SVG ou en PNG.',
            ],
            'evolve' => [
                'title' => 'Vous les faites évoluer',
                'text' => 'Vous changez le contenu ou la destination, sans réimprimer.',
            ],
        ],
        'features' => [
            'title' => 'Fonctionnalités',
            'pages' => [
                'title' => 'Une page par lieu',
                'text' => 'Chaque spot a sa page publique, avec sa carte.',
            ],
            'descriptions' => [
                'title' => 'Des descriptions riches',
                'text' => 'Titres, listes et liens, rédigés en Markdown.',
            ],
            'hidden' => [
                'title' => 'Des spots masqués',
                'text' => 'Hors des listes et de la carte, mais toujours accessibles par leur code QR.',
            ],
            'api' => [
                'title' => 'Une API',
                'text' => 'Pour gérer vos spots depuis vos propres outils, avec des jetons.',
            ],
            'swiss_made' => [
                'title' => 'Conçu en Suisse',
                'text' => 'Conçu et développé en Suisse.',
            ],
            'open_source' => [
                'title' => 'Open source',
                'text' => 'Le code de Spot in® est public, sous licence AGPL-3.0.',
            ],
        ],
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
            'spots' => ':count spot|:count spots',
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
            'description' => 'Les organisations dont vous êtes membre.',
            'empty' => 'Aucune organisation à afficher.',
            'count' => ':count organisation|:count organisations',
            'no_result' => 'Aucune organisation ne correspond à vos filtres.',
            'filter' => [
                'search' => 'Rechercher une organisation',
                'label' => 'Filtrer les organisations',
                'mine' => 'Uniquement les organisations que je gère',
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
            'manage' => [
                'title' => 'Vous pouvez gérer cette organisation',
                'title_member' => 'Vous êtes membre de cette organisation',
                'created' => 'Créée le :date.',
                'members' => ':count membre|:count membres',
                'invitations' => ':count invitation|:count invitations',
                'actions' => [
                    'edit' => 'Modifier',
                ],
            ],
        ],
        'edit' => [
            'title' => "Modifier l'organisation",
            'description' => 'Modifiez ou supprimez une organisation.',
            'danger' => [
                'title' => 'Zone de danger',
            ],
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
            'hidden' => 'Masqué',
            'needs_setup' => 'Pas encore configuré',
            'unnamed' => 'Spot sans nom · :id',
            'forms' => [
                'fields' => [
                    'organization' => [
                        // In the messages of the errors; the form asks the question
                        'label' => 'Organisation',
                        'question' => 'À quelle organisation appartient ce spot ?',
                        'not_allowed' => 'Vous ne pouvez pas créer de spots dans cette organisation.',
                    ],
                    'needs_setup' => [
                        'label' => 'Qui configure le spot ?',
                        'now' => [
                            'label' => 'Je le configure maintenant',
                            'hint' => 'Vous remplissez le spot maintenant. Son nom est obligatoire.',
                        ],
                        'on_site' => [
                            'label' => "Quelqu'un le configurera plus tard",
                            'hint' => 'Remplissez ce que vous savez, ou rien. La première personne qui scanne son code finit de le configurer, même sans compte.',
                        ],
                    ],
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
                    'organization' => 'Organisation',
                    'location' => 'Emplacement',
                    'setup' => 'Configuration',
                    'name_and_description' => 'Nom et description',
                    'website' => 'Site web et visibilité',
                ],
            ],
        ],
        'feedback' => [
            'spot_created' => 'Le spot a été créé.',
            'spot_updated' => 'Le spot a été mis à jour.',
            'spot_set_up' => 'Merci ! Le spot est configuré.',
            'spot_deleted' => 'Le spot a été supprimé.',
        ],
        'index' => [
            'title' => 'Spots',
            'description' => 'Les spots de vos organisations.',
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
        'create' => [
            'title' => 'Nouveau spot',
            'description' => 'Créez un nouveau spot.',
            'introduction' => "Choisissez l'organisation du spot, puis remplissez ses détails.",
            'introduction_in' => 'Vous créez un spot dans :organization.',
            'change_organization' => "Changer d'organisation",
            'form' => [
                'actions' => [
                    'submit' => 'Créer le spot',
                ],
            ],
        ],
        'show' => [
            'scanned' => 'Vous venez de scanner un spot !',
            'description' => 'Les détails du spot.',
            'by' => 'par :organization',
            'fields' => [
                'coordinates' => 'Coordonnées',
            ],
            'visit' => 'Visiter :website',
            'map' => 'Emplacement du spot',
            'manage' => [
                'title' => 'Vous pouvez gérer ce spot',
                'created_by' => 'Créé par :creator le :date.',
                'created' => 'Créé le :date.',
                'actions' => [
                    'edit' => 'Modifier',
                ],
                'qr_code_hint' => 'Ouvre le spot comme un scan. Un spot pas encore configuré ouvre ici votre formulaire : utilisez une fenêtre privée pour voir ce que voient les autres.',
                'badges' => [
                    'redirect' => 'Redirige vers son site web',
                ],
                'notes' => [
                    'redirect' => 'Les visiteurs qui scannent ce spot sont redirigés directement vers :website.',
                    'needs_setup' => "Ce spot n'est pas encore configuré : la première personne qui scanne son code finit de le configurer. D'ici là, seuls les membres de son organisation le trouvent dans les listes.",
                    'hidden' => 'Ce spot est masqué des listes publiques : seuls les membres de :organization le trouvent dans les listes. Il reste accessible par son lien.',
                ],
            ],
        ],
        'setup' => [
            'title' => 'Configurer le spot',
            'description' => 'Configurer un spot sur place.',
            'introduction' => "Ce spot de :organization n'est pas encore configuré : remplissez ses détails pour le configurer. Ses membres pourront les modifier ensuite.",
            'actions' => [
                'submit' => 'Configurer le spot',
            ],
        ],
        'edit' => [
            'title' => 'Modifier le spot',
            'description' => 'Modifiez ou supprimez un spot.',
            'introduction' => "Vous modifiez le spot :spot de l'organisation :organization.",
            'danger' => [
                'title' => 'Zone de danger',
            ],
            'form' => [
                'actions' => [
                    'submit' => 'Enregistrer',
                ],
            ],
        ],
        'delete' => [
            'title' => 'Supprimer le spot',
            'warning' => 'Supprimer le spot le retire pour tout le monde, avec tous ses détails. Ses codes QR imprimés ne mèneront plus nulle part. Cette action est irréversible.',
            'actions' => [
                'submit' => 'Supprimer le spot',
            ],
        ],
    ],
];
