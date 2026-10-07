<?php

declare(strict_types=1);

return [
    'auth' => [
        'register' => [
            'title' => 'Register',
            'description' => 'Create a new account',
            'form' => [
                'fields' => [
                    'username' => [
                        'label' => 'Username',
                        'placeholder' => 'Enter your username',
                    ],
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
                ],
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
                'fields' => [
                    'username_or_email' => [
                        'label' => 'Username or email',
                        'placeholder' => 'Enter your username or email',
                    ],
                    'password' => [
                        'label' => 'Password',
                        'placeholder' => 'Enter your password',
                    ],
                ],
                'actions' => [
                    'submit' => 'Log in',
                ],
            ],
            'remember_me' => 'Remember me',
            'forgot_your_password' => 'Forgot your password?',
            'reset_your_password' => 'Reset your password',
            'dont_have_an_account' => "Don't have an account?",
            'register' => 'Register',
        ],
        'forgot_password' => [
            'title' => 'Forgot your password?',
            'description' => 'Request a password reset link for your account.',
            'form' => [
                'fields' => [
                    'email' => [
                        'label' => 'Email address',
                        'placeholder' => 'Enter your email address',
                    ],
                ],
                'actions' => [
                    'submit' => 'Send reset link',
                ],
            ],
        ],
        'reset_password' => [
            'title' => 'Reset your password',
            'description' => 'Choose a new password for your account.',
            'form' => [
                'fields' => [
                    'email' => [
                        'label' => 'Email address',
                        'placeholder' => 'Enter your email address',
                    ],
                    'password' => [
                        'label' => 'New password',
                        'placeholder' => 'Enter your new password',
                    ],
                    'password_confirmation' => [
                        'label' => 'Password confirmation',
                        'placeholder' => 'Confirm your new password',
                    ],
                ],
                'actions' => [
                    'submit' => 'Reset password',
                ],
            ],
            'already_have_an_account' => 'Already have an account?',
            'login' => 'Log in',
        ],
        'verify_email' => [
            'title' => 'Verify your email address',
            'description' => 'Confirm your email address to complete account setup.',
            'explanation' => 'A verification link has been sent to your email address. Please check your inbox and click the link to verify your email. You will then be able to use your account. If you did not receive the email, you can request another verification link below.',
            'success' => 'A new verification link has been sent to your email address.',
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
                'fields' => [
                    'password' => [
                        'label' => 'Password',
                        'placeholder' => 'Enter your password',
                    ],
                ],
                'actions' => [
                    'submit' => 'Confirm password',
                ],
            ],
        ],
    ],
    'settings' => [
        'title' => 'Settings',
        'profile' => [
            'title' => 'Profile settings',
            'description' => 'Manage your profile information.',
            'form' => [
                'fields' => [
                    'username' => [
                        'label' => 'Username',
                        'placeholder' => 'Enter your username',
                    ],
                    'name' => [
                        'label' => 'Full name',
                        'placeholder' => 'Enter your full name',
                    ],
                    'email' => [
                        'label' => 'Email address',
                        'placeholder' => 'Enter your email address',
                    ],
                ],
                'actions' => [
                    'submit' => 'Save profile',
                ],
            ],
        ],
        'security' => [
            'title' => 'Security settings',
            'description' => 'Update your password.',
            'form' => [
                'fields' => [
                    'current_password' => [
                        'label' => 'Current password',
                        'placeholder' => 'Enter your current password',
                    ],
                    'new_password' => [
                        'label' => 'New password',
                        'placeholder' => 'Enter your new password',
                    ],
                    'confirm_password' => [
                        'label' => 'Confirm new password',
                        'placeholder' => 'Confirm your new password',
                    ],
                ],
                'actions' => [
                    'submit' => 'Change password',
                ],
            ],
        ],
    ],
    'common' => [
        'logout' => 'Logout',
        'theme' => 'Toggle theme',
        'locale' => [
            'label' => 'Change language',
            'options' => [
                'en' => 'English',
                'fr' => 'Français (French)',
            ],
        ],
        'version' => 'Version :version',
        'fill_the_form' => 'Please fill out the form below to continue.',
    ],
    'dashboard' => [
        'title' => 'Dashboard',
        'description' => 'Welcome to your dashboard',
        'first_paragraph' => 'This is the first paragraph of the dashboard.',
        'second_paragraph' => 'This is the second paragraph of the dashboard.',
    ],
    'welcome' => [
        'title' => 'Welcome',
        'description' => 'Welcome to our application',
        'first_paragraph' => 'First paragraph text.',
        'second_paragraph' => 'Second paragraph text.',
        'button' => 'Click me',
    ],
];
