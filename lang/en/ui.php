<?php

declare(strict_types=1);

return [
    'common' => [
        'leave' => [
            'title' => 'You are about to leave :app',
            'warning' => 'This link leads to :website, a website outside of :app, which is not responsible for its content. Continue only if you trust it, and be careful before giving any personal information.',
            'continue' => 'Continue to the website',
        ],
        'scanned' => ':app adds digital information to our physical world with the help of "spots". Share spots with your friends and discover new places and things!',
        'theme' => 'Toggle theme',
        'locale' => [
            'label' => 'Change language',
            'options' => [
                'en' => 'English',
                'fr' => 'Français (French)',
            ],
        ],
        'version' => 'Version :version',
        'account' => 'Account menu',
        'filters' => [
            'search' => 'Search',
            'reset' => 'Reset the filters',
        ],
        'breadcrumbs' => 'Breadcrumb',
        'actions' => [
            'cancel' => 'Cancel',
            'close' => 'Close',
            'see_all' => 'See all',
        ],
        'pagination' => [
            'page' => 'Page :current of :last',
        ],
        'steps' => [
            'previous' => 'Previous',
            'next' => 'Continue',
            'position' => 'Step :current of :total',
        ],
        'map' => [
            'label' => 'Map',
            'hint' => 'Click on the map or drag the marker to set the location.',
            'controls' => [
                'zoom_in' => 'Zoom in',
                'zoom_out' => 'Zoom out',
                'reset_bearing' => 'Reset to north',
                'recenter' => 'Centre on the spot',
                'fullscreen_enter' => 'Enter fullscreen',
                'fullscreen_exit' => 'Exit fullscreen',
                'locate' => 'Show my position',
                'locate_unavailable' => 'Location not available',
                'marker' => 'Map marker',
                'attribution' => 'Show or hide the sources of the map',
                'scroll_windows' => 'Use Ctrl + scroll to zoom the map',
                'scroll_mac' => 'Use ⌘ + scroll to zoom the map',
                'two_fingers' => 'Use two fingers to move the map',
            ],
            'fields' => [
                'latitude' => [
                    'label' => 'Latitude',
                    'placeholder' => 'Enter the latitude, between -90 and 90',
                ],
                'longitude' => [
                    'label' => 'Longitude',
                    'placeholder' => 'Enter the longitude, between -180 and 180',
                ],
            ],
        ],
        'markdown_editor' => [
            'toolbar' => 'Formatting',
            'hint' => 'You can format the text with Markdown.',
            'block' => [
                'label' => 'Type of text',
                'paragraph' => 'Paragraph',
                'heading' => 'Heading :level',
            ],
            'commands' => [
                'bold' => 'Bold',
                'italic' => 'Italic',
                'strike' => 'Strikethrough',
                'code' => 'Code',
                'bullet_list' => 'Bulleted list',
                'ordered_list' => 'Numbered list',
                'blockquote' => 'Quote',
                'code_block' => 'Code block',
                'horizontal_rule' => 'Horizontal line',
                'link' => 'Link',
                'image' => 'Image',
            ],
            'prompts' => [
                'link' => 'Address of the link (leave empty to remove the link):',
                'image' => 'Address of the image:',
                'image_alt' => 'Description of the image, for the people who cannot see it:',
            ],
            'markdown' => 'Show the Markdown code',
        ],
    ],
    'auth' => [
        'common' => [
            'forms' => [
                'fields' => [
                    'name' => [
                        'label' => 'Full name',
                        'placeholder' => 'Enter your full name',
                    ],
                    'email' => [
                        'label' => 'Email address',
                        'placeholder' => 'Enter your email address',
                    ],
                    'password' => [
                        'label' => 'Password',
                        'placeholder' => 'Enter your password',
                    ],
                    'password_confirmation' => [
                        'label' => 'Password confirmation',
                        'placeholder' => 'Confirm your password',
                    ],
                    'remember' => [
                        'label' => 'Keep me logged in on this device',
                    ],
                    'current_password' => [
                        'label' => 'Current password',
                        'placeholder' => 'Enter your current password',
                    ],
                    'new_password' => [
                        'label' => 'New password',
                        'placeholder' => 'Enter your new password',
                    ],
                    'new_password_confirmation' => [
                        'label' => 'Confirm new password',
                        'placeholder' => 'Confirm your new password',
                    ],
                ],
            ],
        ],
        'feedback' => [
            'verification_link_sent' => 'A new verification link has been sent to your email address.',
        ],
        'register' => [
            'title' => 'Register',
            'description' => 'Create a new account',
            'invitation' => 'You are creating an account to join :organization. Your invitation was sent to this email address, so it cannot be changed.',
            'steps' => [
                'information' => [
                    'title' => 'Information',
                    'audience' => 'Spot in® is made for public administrations and organizations that manage spots. It is not meant for personal use.',
                    'membership' => 'Registering does not let you create spots on its own: an organization must first invite you to join it. Only the members of an organization can create its spots.',
                    'discover' => 'To discover the spots, you do not need an account.',
                ],
                'account' => [
                    'title' => 'Account',
                ],
            ],
            'form' => [
                'actions' => [
                    'submit' => 'Register',
                ],
            ],
            'already_have_an_account' => 'Already have an account?',
            'login' => 'Log in',
        ],
        'login' => [
            'title' => 'Login',
            'description' => 'Access your account',
            'form' => [
                'options' => 'Login options',
                'actions' => [
                    'submit' => 'Log in',
                ],
            ],
            'forgot_your_password' => 'Forgot your password?',
            'reset_your_password' => 'Reset your password',
            'dont_have_an_account' => "Don't have an account?",
            'register' => 'Register',
        ],
        'forgot_password' => [
            'title' => 'Forgot your password?',
            'description' => 'Request a password reset link for your account.',
            'form' => [
                'actions' => [
                    'submit' => 'Send reset link',
                ],
            ],
        ],
        'reset_password' => [
            'title' => 'Reset your password',
            'description' => 'Choose a new password for your account.',
            'form' => [
                'actions' => [
                    'submit' => 'Reset password',
                ],
            ],
        ],
        'verify_email' => [
            'title' => 'Verify your email address',
            'description' => 'Confirm your email address to complete account setup.',
            'explanation' => 'A verification link has been sent to your email address. Please check your inbox and click the link to verify your email. You will then be able to use your account. If you did not receive the email, you can request another verification link below.',
            'form' => [
                'actions' => [
                    'resend' => 'Resend verification email',
                ],
            ],
        ],
        'password_confirmation' => [
            'title' => 'Confirm your password',
            'description' => 'Confirm your password to continue.',
            'explanation' => 'For security reasons, please confirm your password before continuing.',
            'form' => [
                'actions' => [
                    'submit' => 'Confirm password',
                ],
            ],
        ],
        'logout' => 'Logout',
    ],
    'dashboard' => [
        'title' => 'Dashboard',
        'description' => 'Your organizations, spots and invitations.',
        'greeting' => 'Hello, :name!',
        'organizations' => [
            'title' => 'My organizations',
            'empty' => 'You do not belong to any organization yet. Ask a responsible user of an organization to invite you.',
        ],
        'spots' => [
            'title' => 'My spots',
            'empty' => 'You have not created any spot yet.',
        ],
    ],
    'homepage' => [
        'title' => 'Welcome to Spot in®',
        'description' => 'Discover locations and places of interest in your area.',
        'introduction' => 'Spots represent locations or places of interest in our physical world. Discover spots in your area and find new places to visit!',
        'explore_spots' => 'Explore the spots',
        'explore_organizations' => 'Browse the organizations',
        'dashboard' => 'Go to my dashboard',
        'latest_spots' => 'Latest spots',
        'latest_spots_empty' => 'No spot has been created yet.',
    ],
    'invitations' => [
        'common' => [
            'alert' => [
                'message' => 'You have :count pending invitation.|You have :count pending invitations.',
                'action' => 'View invitations',
            ],
        ],
        'feedback' => [
            'invitation_accepted' => 'You have joined the organization.',
            'invitation_declined' => 'The invitation has been declined.',
            'invalid_invitation' => 'This invitation is invalid, has expired, or was sent to another email address.',
        ],
        'index' => [
            'title' => 'Pending invitations',
            'description' => 'The invitations you have received to join an organization.',
            'table' => [
                'empty' => 'You have no pending invitation.',
                'invited_by' => 'Invited by :name',
                'columns' => [
                    'organization' => 'Organization',
                    'role' => 'Role',
                    'actions' => 'Actions',
                ],
                'actions' => [
                    'accept' => 'Accept',
                    'decline' => [
                        'label' => 'Decline',
                        'title' => 'Decline the invitation',
                        'warning' => 'You will not join this organization. A responsible user will have to invite you again if you change your mind.',
                        'actions' => [
                            'submit' => 'Decline the invitation',
                        ],
                    ],
                ],
            ],
        ],
        'show' => [
            'title' => 'Join an organization',
            'description' => 'Accept your invitation to join an organization.',
            'explanation' => 'You have been invited to join :organization as :role.',
            'actions' => [
                'register' => 'Create an account',
                'login' => 'I already have an account',
            ],
        ],
    ],
    'organizations' => [
        'common' => [
            'forms' => [
                'fields' => [
                    'name' => [
                        'label' => 'Name',
                        'placeholder' => 'Enter the organization name',
                    ],
                    'role' => [
                        'label' => 'Role',
                        'placeholder' => 'Select the role',
                        'options' => [
                            'responsible' => 'Responsible',
                            'member' => 'Member',
                        ],
                    ],
                ],
            ],
        ],
        'feedback' => [
            'organization_created' => 'The organization has been created.',
            'organization_updated' => 'The organization has been updated.',
            'organization_deleted' => 'The organization has been deleted.',
        ],
        'index' => [
            'create' => 'New organization',
            'title' => 'Organizations',
            'description' => 'Discover the organizations of the platform.',
            'empty' => 'No organization to show.',
            'count' => ':count organization|:count organizations',
            'no_result' => 'No organization matches your filters.',
            'filter' => [
                'search' => 'Search an organization',
                'label' => 'Filter the organizations',
                'mine' => 'Only the organizations I belong to',
            ],
        ],
        'create' => [
            'title' => 'New organization',
            'description' => 'Create a new organization.',
            'form' => [
                'actions' => [
                    'submit' => 'Create the organization',
                ],
            ],
        ],
        'show' => [
            'scanned' => 'You have just scanned an organization!',
            'description' => 'The spots, members and invitations of the organization.',
            'actions' => [
                'edit' => 'Edit',
                'delete' => 'Delete',
            ],
            'sections' => [
                'members' => [
                    'title' => 'Members',
                    'summary' => ':count member|:count members',
                ],
                'spots' => [
                    'title' => 'Spots',
                    'summary' => ':count spot|:count spots',
                ],
                'invitations' => [
                    'title' => 'Invitations',
                    'summary' => ':count pending invitation|:count pending invitations',
                ],
            ],
        ],
        'edit' => [
            'title' => 'Edit the organization',
            'description' => 'Edit or delete an organization.',
            'form' => [
                'actions' => [
                    'submit' => 'Save',
                ],
            ],
        ],
        'delete' => [
            'title' => 'Delete the organization',
            'warning' => 'This permanently deletes the organization, its memberships and its pending invitations. This cannot be undone.',
            'actions' => [
                'submit' => 'Delete the organization',
            ],
        ],
        'invitations' => [
            'common' => [
                'mail' => [
                    'subject' => 'You have been invited to join :organization',
                    'line' => 'You have been invited to join :organization as :role.',
                    'action' => 'View the invitation',
                    'expires' => 'This invitation expires on :date.',
                    'ignore' => 'If you were not expecting this invitation, you can ignore this email.',
                ],
            ],
            'feedback' => [
                'invitation_sent' => 'The invitation has been sent.',
                'invitation_revoked' => 'The invitation has been revoked.',
                'already_member' => 'This person already belongs to the organization.',
            ],
            'index' => [
                'title' => 'Invitations',
                'description' => 'The pending invitations to join the organization.',
                'invite' => 'Invite someone',
                'table' => [
                    'empty' => 'There is no pending invitation.',
                    'expired' => 'Expired',
                    'columns' => [
                        'email' => 'Email address',
                        'role' => 'Role',
                        'expires' => 'Expires on',
                        'actions' => 'Actions',
                    ],
                    'actions' => [
                        'resend' => 'Resend',
                        'revoke' => [
                            'label' => 'Revoke',
                            'title' => 'Revoke the invitation',
                            'warning' => 'This permanently revokes the invitation. The person will not be able to accept it anymore.',
                            'actions' => [
                                'submit' => 'Revoke the invitation',
                            ],
                        ],
                    ],
                ],
            ],
            'create' => [
                'title' => 'Invite someone',
                'description' => 'Invite someone to join the organization.',
                'fields' => [
                    'email' => [
                        'label' => 'Email address',
                        'placeholder' => 'Enter the email address of the person to invite',
                    ],
                ],
                'actions' => [
                    'submit' => 'Send the invitation',
                ],
            ],
        ],
        'members' => [
            'feedback' => [
                'member_role_updated' => 'The role has been updated.',
                'member_removed' => 'The member has been removed.',
                'not_a_member' => 'This person does not belong to the organization.',
                'last_responsible' => 'An organization must keep at least one responsible user.',
                'organization_left' => 'You have left the organization.',
            ],
            'index' => [
                'title' => 'Members',
                'description' => 'The members of the organization and their roles.',
                'table' => [
                    'empty' => 'There is no member yet.',
                    'you' => 'You',
                    'columns' => [
                        'name' => 'Name',
                        'role' => 'Role',
                        'actions' => 'Actions',
                    ],
                    'actions' => [
                        'invite' => 'Invite someone',
                        'save' => 'Save',
                        'leave' => [
                            'label' => 'Leave',
                            'title' => 'Leave the organization',
                            'warning' => 'You will lose access to this organization. A responsible user will have to invite you again if you want to come back.',
                            'confirm' => 'Leave the organization',
                            'last_responsible' => 'You are the only responsible user of this organization. Promote another member to responsible before you can leave.',
                        ],
                        'remove' => [
                            'label' => 'Remove',
                            'title' => 'Remove the member',
                            'warning' => 'This person will lose access to the organization. A responsible user will have to invite them again if they want to come back.',
                            'actions' => [
                                'submit' => 'Remove the member',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'spots' => [
            'feedback' => [
                'spot_created' => 'The spot has been created.',
            ],
            'create' => [
                'title' => 'New spot',
                'description' => 'Create a new spot.',
                'introduction' => 'You are about to create a new spot in the organization :organization.',
                'form' => [
                    'actions' => [
                        'submit' => 'Create the spot',
                    ],
                ],
            ],
        ],
    ],
    'settings' => [
        'title' => 'Settings',
        'description' => 'Manage your account.',
        'developer' => [
            'common' => [
                'forms' => [
                    'fields' => [
                        'name' => [
                            'label' => 'Name',
                            'placeholder' => 'Enter a name for the token',
                        ],
                        'abilities' => [
                            'label' => 'Abilities',
                            'options' => [
                                'organizations_read' => 'Read organizations',
                                'organizations_create' => 'Create organizations',
                                'organizations_update' => 'Update organizations',
                                'organizations_delete' => 'Delete organizations',
                                'spots_read' => 'Read spots',
                                'spots_create' => 'Create spots',
                                'spots_update' => 'Update spots',
                                'spots_delete' => 'Delete spots',
                            ],
                        ],
                        'expiry' => [
                            'label' => 'Expiration',
                            'placeholder' => 'Select the expiration',
                            'options' => [
                                '30' => '30 days',
                                '90' => '90 days',
                                '365' => '1 year',
                                'never' => 'Never',
                            ],
                        ],
                    ],
                ],
            ],
            'feedback' => [
                'token_created' => 'The token has been created. Copy this token now. For security reasons, it will not be shown again.',
                'token_revoked' => 'The token has been revoked.',
            ],
            'index' => [
                'title' => 'Developer settings',
                'description' => 'Manage the personal access tokens used to access the API.',
                'warning' => 'This section is for advanced users only. Personal access tokens give an application access to the API on your behalf: only create one if you know what you are doing, keep it secret and revoke it when you no longer need it.',
                'access_the_api' => 'The API documentation is available at :ui and :document.',
                'your_tokens' => 'Your tokens',
                'table' => [
                    'empty' => 'You have no token yet.',
                    'never_used' => 'Never used',
                    'never_expires' => 'Never expires',
                    'last_used' => 'Last used :when',
                    'expires' => 'Expires on :date',
                    'expired' => 'Expired',
                    'columns' => [
                        'name' => 'Name',
                        'abilities' => 'Abilities',
                        'actions' => 'Actions',
                    ],
                    'actions' => [
                        'revoke' => [
                            'label' => 'Revoke',
                            'title' => 'Revoke the token',
                            'warning' => 'Applications using this token will immediately lose access. This cannot be undone.',
                            'actions' => [
                                'submit' => 'Revoke the token',
                            ],
                        ],
                    ],
                ],
            ],
            'create' => [
                'title' => 'New token',
                'description' => 'A token gives an application access to the API on your behalf, within the abilities you choose.',
                'form' => [
                    'actions' => [
                        'submit' => 'Create the token',
                    ],
                ],
            ],
        ],
        'profile' => [
            'title' => 'Profile settings',
            'description' => 'Manage your profile information.',
            'feedback' => [
                'profile_information_updated' => 'Your profile has been updated.',
            ],
            'form' => [
                'actions' => [
                    'submit' => 'Save profile',
                ],
            ],
        ],
        'security' => [
            'title' => 'Security settings',
            'description' => 'Manage your security preferences.',
            'feedback' => [
                'password_updated' => 'Your password has been updated.',
            ],
            'form' => [
                'actions' => [
                    'submit' => 'Change password',
                ],
            ],
        ],
    ],
    'spots' => [
        'common' => [
            'unknown_creator' => 'Unknown',
            'hidden' => 'Hidden',
            'forms' => [
                'fields' => [
                    'name' => [
                        'label' => 'Name',
                        'placeholder' => 'Enter the spot name',
                    ],
                    'description' => [
                        'label' => 'Description',
                        'placeholder' => 'Describe the spot',
                    ],
                    'website' => [
                        'label' => 'Website',
                        'placeholder' => 'https://example.com',
                    ],
                    'redirect_to_website' => [
                        'label' => 'Redirect the visitors directly to the website',
                        'hint' => 'The visitors who scan the spot go straight to the website, without seeing its page. Only applies when the spot has a website.',
                        'needs_website' => 'Add a website to redirect the visitors to, or turn off the redirect.',
                    ],
                    'is_hidden' => [
                        'label' => 'Hide the spot from the public lists',
                        'hint' => 'Only the members of the organization find the spot in the lists and the search. The spot stays reachable by its link.',
                    ],
                ],
                'steps' => [
                    'location' => 'Location',
                    'details' => 'Details',
                    'website' => 'Website and visibility',
                ],
            ],
        ],
        'feedback' => [
            'spot_updated' => 'The spot has been updated.',
            'spot_deleted' => 'The spot has been deleted.',
        ],
        'index' => [
            'title' => 'Spots',
            'description' => 'Discover the spots of the platform.',
            'empty' => 'No spot to show.',
            'count' => ':count spot|:count spots',
            'no_result' => 'No spot matches your filters.',
            'filter' => [
                'search' => 'Search a spot',
                'label' => 'Filter the spots',
                'mine' => 'Only the spots I created',
            ],
        ],
        'show' => [
            'scanned' => 'You have just scanned a spot!',
            'description' => 'The details of the spot.',
            'actions' => [
                'label' => 'Actions',
                'like' => 'Like',
                'report' => 'Report',
                'share' => 'Share',
                'edit' => 'Edit',
                'delete' => 'Delete',
            ],
            'fields' => [
                'organization' => 'Organization',
                'creator' => 'Created by',
                'created' => 'Created on',
                'coordinates' => 'Coordinates',
            ],
            'visit' => 'Visit :website',
            'map' => 'Location of the spot',
            'notes' => [
                'redirect' => 'The visitors who scan this spot are redirected directly to :website.',
                'hidden' => 'This spot is hidden from the public lists: only the members of :organization find it in the lists. It stays reachable by its link.',
            ],
        ],
        'edit' => [
            'title' => 'Edit the spot',
            'description' => 'Edit or delete a spot.',
            'introduction' => 'You are editing the spot :spot in the organization :organization.',
            'form' => [
                'actions' => [
                    'submit' => 'Save',
                ],
            ],
        ],
        'delete' => [
            'title' => 'Delete the spot',
            'warning' => 'This permanently deletes the spot. This cannot be undone.',
            'actions' => [
                'submit' => 'Delete the spot',
            ],
        ],
    ],
];
