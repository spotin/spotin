<?php

declare(strict_types=1);

return [
    'common' => [
        'theme' => 'Toggle theme',
        'locale' => [
            'label' => 'Change language',
            'options' => [
                'en' => 'English',
                'fr' => 'Français (French)',
            ],
        ],
        'version' => 'Version :version',
        'fill-the-form' => 'Please fill out the form below to continue.',
        'account' => 'Account menu',
        'filters' => [
            'search' => 'Search',
            'reset' => 'Reset the filters',
        ],
        'breadcrumbs' => 'Breadcrumb',
        'actions' => [
            'cancel' => 'Cancel',
            'close' => 'Close',
            'see-all' => 'See all',
        ],
        'pagination' => [
            'page' => 'Page :current of :last',
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
                    'password-confirmation' => [
                        'label' => 'Password confirmation',
                        'placeholder' => 'Confirm your password',
                    ],
                    'remember' => [
                        'label' => 'Remember me',
                    ],
                    'current-password' => [
                        'label' => 'Current password',
                        'placeholder' => 'Enter your current password',
                    ],
                    'new-password' => [
                        'label' => 'New password',
                        'placeholder' => 'Enter your new password',
                    ],
                    'new-password-confirmation' => [
                        'label' => 'Confirm new password',
                        'placeholder' => 'Confirm your new password',
                    ],
                ],
            ],
        ],
        'feedback' => [
            'verification-link-sent' => 'A new verification link has been sent to your email address.',
        ],
        'register' => [
            'title' => 'Register',
            'description' => 'Create a new account',
            'invitation' => 'You are creating an account to join :organization. Your invitation was sent to this email address, so it cannot be changed.',
            'form' => [
                'actions' => [
                    'submit' => 'Register',
                ],
            ],
            'already-have-an-account' => 'Already have an account?',
            'login' => 'Log in',
        ],
        'login' => [
            'title' => 'Login',
            'description' => 'Access your account',
            'form' => [
                'actions' => [
                    'submit' => 'Log in',
                ],
            ],
            'forgot-your-password' => 'Forgot your password?',
            'reset-your-password' => 'Reset your password',
            'dont-have-an-account' => "Don't have an account?",
            'register' => 'Register',
        ],
        'forgot-password' => [
            'title' => 'Forgot your password?',
            'description' => 'Request a password reset link for your account.',
            'form' => [
                'actions' => [
                    'submit' => 'Send reset link',
                ],
            ],
        ],
        'reset-password' => [
            'title' => 'Reset your password',
            'description' => 'Choose a new password for your account.',
            'form' => [
                'actions' => [
                    'submit' => 'Reset password',
                ],
            ],
        ],
        'verify-email' => [
            'title' => 'Verify your email address',
            'description' => 'Confirm your email address to complete account setup.',
            'explanation' => 'A verification link has been sent to your email address. Please check your inbox and click the link to verify your email. You will then be able to use your account. If you did not receive the email, you can request another verification link below.',
            'form' => [
                'actions' => [
                    'resend' => 'Resend verification email',
                ],
            ],
        ],
        'password-confirmation' => [
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
        'title' => 'Welcome to Spot in',
        'description' => 'Discover locations and places of interest in your area.',
        'intro' => 'Spots represent locations or places of interest in our physical world. Discover spots in your area and find new places to visit!',
        'explore-spots' => 'Explore the spots',
        'explore-organizations' => 'Browse the organizations',
        'dashboard' => 'Go to my dashboard',
        'latest-spots' => 'Latest spots',
        'latest-spots-empty' => 'No spot has been created yet.',
    ],
    'organizations' => [
        'common' => [
            'forms' => [
                'fields' => [
                    'name' => [
                        'label' => 'Organization name',
                        'placeholder' => 'Enter the organization name',
                    ],
                    'role' => [
                        'label' => 'Role',
                        'options' => [
                            'responsible' => 'Responsible',
                            'member' => 'Member',
                        ],
                    ],
                ],
            ],
        ],
        'feedback' => [
            'organization-created' => 'The organization has been created.',
            'organization-updated' => 'The organization has been updated.',
            'organization-deleted' => 'The organization has been deleted.',
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
        'index' => [
            'create' => 'New organization',
            'title' => 'Organizations',
            'description' => 'Discover the organizations of the platform.',
            'empty' => 'No organization to show.',
            'count' => ':count organization|:count organizations',
            'no-result' => 'No organization matches your filters.',
            'filter' => [
                'search' => 'Search an organization',
                'label' => 'Filter the organizations',
                'mine' => 'Only the organizations I belong to',
            ],
            'pending-invitations' => 'You have :count pending invitation.|You have :count pending invitations.',
            'view-pending-invitations' => 'View invitations',
        ],
        'show' => [
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
        'invitations' => [
            'feedback' => [
                'invitation-sent' => 'The invitation has been sent.',
                'invitation-revoked' => 'The invitation has been revoked.',
                'invitation-accepted' => 'You have joined the organization.',
                'invitation-declined' => 'The invitation has been declined.',
                'invalid-invitation' => 'This invitation is invalid, has expired, or was sent to another email address.',
                'already-member' => 'This person already belongs to the organization.',
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
                        'revoke' => 'Revoke',
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
                'mail' => [
                    'subject' => 'You have been invited to join :organization',
                    'line' => 'You have been invited to join :organization as :role.',
                    'action' => 'View the invitation',
                    'expires' => 'This invitation expires on :date.',
                    'ignore' => 'If you were not expecting this invitation, you can ignore this email.',
                ],
            ],
            'pending' => [
                'title' => 'Pending invitations',
                'description' => 'The invitations you have received to join an organization.',
                'table' => [
                    'empty' => 'You have no pending invitation.',
                    'invited-by' => 'Invited by :name',
                    'columns' => [
                        'organization' => 'Organization',
                        'role' => 'Role',
                        'actions' => 'Actions',
                    ],
                    'actions' => [
                        'accept' => 'Accept',
                        'decline' => 'Decline',
                    ],
                ],
            ],
            'decline' => [
                'title' => 'Decline the invitation',
                'warning' => 'You will not join this organization. A responsible user will have to invite you again if you change your mind.',
                'actions' => [
                    'submit' => 'Decline the invitation',
                ],
            ],
            'revoke' => [
                'title' => 'Revoke the invitation',
                'warning' => 'This permanently revokes the invitation. The person will not be able to accept it anymore.',
                'actions' => [
                    'submit' => 'Revoke the invitation',
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
        'members' => [
            'feedback' => [
                'member-role-updated' => 'The role has been updated.',
                'member-removed' => 'The member has been removed.',
                'not-a-member' => 'This person does not belong to the organization.',
                'last-responsible' => 'An organization must keep at least one responsible user.',
                'organization-left' => 'You have left the organization.',
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
                        'leave' => 'Leave',
                        'remove' => 'Remove',
                    ],
                ],
            ],
            'remove' => [
                'title' => 'Remove the member',
                'warning' => 'This person will lose access to the organization. A responsible user will have to invite them again if they want to come back.',
                'actions' => [
                    'submit' => 'Remove the member',
                ],
            ],
            'leave' => [
                'title' => 'Leave the organization',
                'warning' => 'You will lose access to this organization. A responsible user will have to invite you again if you want to come back.',
                'confirm' => 'Leave the organization',
                'last-responsible' => 'You are the only responsible user of this organization. Promote another member to responsible before you can leave.',
            ],
        ],
    ],
    'settings' => [
        'title' => 'Settings',
        'description' => 'Manage your account.',
        'feedback' => [
            'profile-information-updated' => 'Your profile has been updated.',
            'password-updated' => 'Your password has been updated.',
        ],
        'profile' => [
            'title' => 'Profile settings',
            'description' => 'Manage your profile information.',
            'form' => [
                'actions' => [
                    'submit' => 'Save profile',
                ],
            ],
        ],
        'security' => [
            'title' => 'Security settings',
            'description' => 'Update your password.',
            'form' => [
                'actions' => [
                    'submit' => 'Change password',
                ],
            ],
        ],
    ],
    'spots' => [
        'index' => [
            'create' => 'New spot',
            'title' => 'Spots',
            'description' => 'Discover the spots of the platform.',
            'empty' => 'No spot to show.',
            'count' => ':count spot|:count spots',
            'no-result' => 'No spot matches your filters.',
            'filter' => [
                'search' => 'Search a spot',
                'label' => 'Filter the spots',
                'mine' => 'Only the spots I created',
            ],
        ],
        'common' => [
            'unknown-creator' => 'Unknown',
            'forms' => [
                'fields' => [
                    'name' => [
                        'label' => 'Spot name',
                        'placeholder' => 'Enter the spot name',
                    ],
                    'organization' => [
                        'label' => 'Organization',
                        'placeholder' => 'Select an organization',
                    ],
                ],
            ],
        ],
        'feedback' => [
            'spot-created' => 'The spot has been created.',
            'spot-updated' => 'The spot has been updated.',
            'spot-deleted' => 'The spot has been deleted.',
        ],
        'create' => [
            'title' => 'New spot',
            'description' => 'Create a new spot.',
            'no-organization' => 'You need to belong to an organization to create a spot.',
            'form' => [
                'actions' => [
                    'submit' => 'Create the spot',
                ],
            ],
        ],
        'show' => [
            'description' => 'The details of the spot.',
            'actions' => [
                'edit' => 'Edit',
                'delete' => 'Delete',
            ],
            'fields' => [
                'organization' => 'Organization',
                'creator' => 'Created by',
                'created' => 'Created on',
            ],
        ],
        'edit' => [
            'title' => 'Edit the spot',
            'description' => 'Edit or delete a spot.',
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
