<?php

declare(strict_types=1);

return [
    'common' => [
        'qr_code' => [
            'label' => 'QR code',
            'download_svg' => 'Download as SVG',
            'download_png' => 'Download as PNG',
            'scan_link' => 'Scan link',
            'open' => 'Open the scan link',
            'hint' => 'Opens the page as a scan does.',
            'copy' => 'Copy',
            'copied' => 'Copied',
        ],
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
            'label' => 'Actions',
            'like' => 'Like',
            'report' => 'Report',
            'share' => 'Share',
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
            'hint' => 'Click on the map or drag the marker to set the location. Leave the coordinates empty if the spot has no location.',
            'incomplete' => 'Enter both the latitude and the longitude, or leave both empty.',
            'spot_link' => 'See the spot',
            'controls' => [
                'zoom_in' => 'Zoom in',
                'zoom_out' => 'Zoom out',
                'reset_bearing' => 'Reset to north',
                'recenter' => 'Centre on the spot',
                'recenter_spots' => 'Show all the spots',
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
            'invitation' => 'You are creating an account to join :organization, which you join as soon as your account is created. Your invitation was sent to this email address, so it cannot be changed.',
            'invitation_only' => [
                'title' => 'Accounts are created by invitation',
                'text' => 'To create an account, follow the link of the invitation an organization sent you by email. To discover the spots, you do not need an account.',
                'contact' => 'Your institution would like to use Spot in®? Contact us.',
                'required' => 'An account can only be created by following an invitation, with the email address it was sent to.',
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
            'reset_your_password' => 'Reset your password.',
            'dont_have_an_account' => "Don't have an account?",
            'contact' => 'Contact us and we can invite you to the platform.',
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
    'contact' => [
        'title' => 'Contact us',
        'description' => 'Your institution would like to use Spot in®? Write to us, we will get back to you quickly.',
        'fields' => [
            'name' => [
                'label' => 'Name',
                'placeholder' => 'Enter your name',
            ],
            'email' => [
                'label' => 'Email address',
                'placeholder' => 'Enter your email address',
            ],
            'organization' => [
                'label' => 'Institution',
                'placeholder' => 'Enter the name of your institution',
            ],
            'message' => [
                'label' => 'Message',
                'placeholder' => 'Tell us more about your needs',
            ],
            'website' => [
                'label' => 'Leave this field empty',
            ],
        ],
        'actions' => [
            'submit' => 'Send',
        ],
        'feedback' => [
            'sent' => 'Thank you for your message! We will get back to you quickly.',
        ],
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
        'title' => 'QR codes that never expire',
        'description' => 'The platform for public institutions to create and manage their QR codes.',
        'contact' => 'Contact us',
        'audience' => 'For municipalities, regions, museums, libraries and tourist offices.',
        'map' => 'Map of all the spots',
        'explore_spots' => 'Explore the spots',
        'explore_organizations' => 'Browse the organizations',
        'dashboard' => 'Go to my dashboard',
        'call_to_action' => [
            'title' => 'Your institution is interested?',
            'text' => 'Tell us about your needs: we create your organization and help you get started.',
        ],
        'advantages' => [
            'title' => 'Why Spot in®',
            'lasting' => [
                'title' => 'Printed once, valid forever',
                'text' => 'Your QR codes lead to Spot in®, and you change where they lead at any time, without reprinting anything. No more QR codes leading to pages that are gone.',
            ],
            'content' => [
                'title' => 'Content you control',
                'text' => 'Each QR code represents a place or an entity, with its own page, or redirects to the website of your choice.',
            ],
            'governance' => [
                'title' => 'A clear framework',
                'text' => 'You decide who creates and manages the QR codes of your organization, with the roles of responsible and member.',
            ],
        ],
        'workflow' => [
            'title' => 'How it works',
            'organization' => [
                'title' => 'We create your organization',
                'text' => 'You contact us, we open the space of your institution, and you become its responsible.',
            ],
            'members' => [
                'title' => 'You invite your colleagues',
                'text' => 'As responsibles or as members.',
            ],
            'spots' => [
                'title' => 'You create your spots',
                'text' => 'A place, an entity or a redirect to your website, or a code to set up on site by the first person who scans it.',
            ],
            'print' => [
                'title' => 'You print the QR codes',
                'text' => 'As SVG or PNG.',
            ],
            'evolve' => [
                'title' => 'You keep them up to date',
                'text' => 'You change the content or the destination, without reprinting.',
            ],
        ],
        'features' => [
            'title' => 'Features',
            'pages' => [
                'title' => 'A page per place',
                'text' => 'Each spot has its public page, with its map.',
            ],
            'descriptions' => [
                'title' => 'Rich descriptions',
                'text' => 'Headings, lists and links, written in Markdown.',
            ],
            'hidden' => [
                'title' => 'Hidden spots',
                'text' => 'Out of the lists and the map, but still reachable by their QR code.',
            ],
            'api' => [
                'title' => 'An API',
                'text' => 'To manage your spots from your own tools, with tokens.',
            ],
            'swiss_made' => [
                'title' => 'Made in Switzerland',
                'text' => 'Designed and developed in Switzerland.',
            ],
            'open_source' => [
                'title' => 'Open source',
                'text' => 'The code of Spot in® is public, under the AGPL-3.0 license.',
            ],
        ],
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
            'spots' => ':count spot|:count spots',
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
            'map' => 'Spots of the organization',
            'description' => 'The spots, members and invitations of the organization.',
            'manage' => [
                'title' => 'You can manage this organization',
                'title_member' => 'You are a member of this organization',
                'created' => 'Created on :date.',
                'members' => ':count member|:count members',
                'invitations' => ':count invitation|:count invitations',
                'actions' => [
                    'edit' => 'Edit',
                ],
            ],
        ],
        'edit' => [
            'title' => 'Edit the organization',
            'description' => 'Edit or delete an organization.',
            'danger' => [
                'title' => 'Danger zone',
            ],
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
            'hidden' => 'Hidden',
            'needs_setup' => 'Not set up yet',
            'unnamed' => 'Unnamed spot · :id',
            'forms' => [
                'fields' => [
                    'organization' => [
                        // In the messages of the errors; the form asks the question
                        'label' => 'Organization',
                        'question' => 'Which organization does this spot belong to?',
                        'not_allowed' => 'You cannot create spots in this organization.',
                    ],
                    'needs_setup' => [
                        'label' => 'Who sets up the spot?',
                        'now' => [
                            'label' => "I'll set it up now",
                            'hint' => 'You fill in the spot now. Its name is required.',
                        ],
                        'on_site' => [
                            'label' => 'Someone will set it up later',
                            'hint' => 'Fill in what you know, or nothing. The first person who scans its code finishes setting it up, even without an account.',
                        ],
                    ],
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
                    'organization' => 'Organization',
                    'location' => 'Location',
                    'setup' => 'Setup',
                    'name_and_description' => 'Name and description',
                    'website' => 'Website and visibility',
                ],
            ],
        ],
        'feedback' => [
            'spot_created' => 'The spot has been created.',
            'spot_updated' => 'The spot has been updated.',
            'spot_set_up' => 'Thank you! The spot is set up.',
            'spot_deleted' => 'The spot has been deleted.',
        ],
        'index' => [
            'title' => 'Spots',
            'description' => 'Discover the spots of the platform.',
            'empty' => 'No spot to show.',
            'count' => ':count spot|:count spots',
            'map' => 'Map of the spots',
            'no_result' => 'No spot matches your filters.',
            'filter' => [
                'search' => 'Search a spot',
                'label' => 'Filter the spots',
                'mine' => 'Only the spots I created',
            ],
        ],
        'create' => [
            'title' => 'New spot',
            'description' => 'Create a new spot.',
            'introduction' => 'Choose the organization of the spot, then fill in its details.',
            'introduction_in' => 'You are creating a spot in :organization.',
            'change_organization' => 'Change the organization',
            'form' => [
                'actions' => [
                    'submit' => 'Create the spot',
                ],
            ],
        ],
        'show' => [
            'scanned' => 'You have just scanned a spot!',
            'description' => 'The details of the spot.',
            'by' => 'by :organization',
            'fields' => [
                'coordinates' => 'Coordinates',
            ],
            'visit' => 'Visit :website',
            'map' => 'Location of the spot',
            'manage' => [
                'title' => 'You can manage this spot',
                'created_by' => 'Created by :creator on :date.',
                'created' => 'Created on :date.',
                'actions' => [
                    'edit' => 'Edit',
                ],
                'qr_code_hint' => 'Opens the spot as a scan does. A spot that is not set up yet opens your form here: use a private window to see what the others see.',
                'badges' => [
                    'redirect' => 'Redirects to its website',
                ],
                'notes' => [
                    'redirect' => 'The visitors who scan this spot are redirected directly to :website.',
                    'needs_setup' => 'This spot is not set up yet: the first person who scans its code finishes setting it up. Only the members of its organization find it in the lists until then.',
                    'hidden' => 'This spot is hidden from the public lists: only the members of :organization find it in the lists. It stays reachable by its link.',
                ],
            ],
        ],
        'setup' => [
            'title' => 'Set up the spot',
            'description' => 'Set up a spot on site.',
            'introduction' => 'This spot of :organization is not set up yet: fill in its details to set it up. Its members can change them afterwards.',
            'actions' => [
                'submit' => 'Set up the spot',
            ],
        ],
        'edit' => [
            'title' => 'Edit the spot',
            'description' => 'Edit or delete a spot.',
            'introduction' => 'You are editing the spot :spot in the organization :organization.',
            'danger' => [
                'title' => 'Danger zone',
            ],
            'form' => [
                'actions' => [
                    'submit' => 'Save',
                ],
            ],
        ],
        'delete' => [
            'title' => 'Delete the spot',
            'warning' => 'Deleting the spot removes it for everyone, with all its details. Its printed QR codes will lead nowhere. This cannot be undone.',
            'actions' => [
                'submit' => 'Delete the spot',
            ],
        ],
    ],
];
