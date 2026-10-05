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
                        'hint' => 'Use 3 to 30 characters containing only letters, numbers, hyphens or underscores.',
                    ],
                    'name' => [
                        'label' => 'Full name',
                        'placeholder' => 'Enter your full name',
                        'hint' => 'Use 2 to 50 characters containing only letters, spaces, apostrophes or hyphens.',
                    ],
                    'email' => [
                        'label' => 'Email address',
                        'placeholder' => 'Enter your email address',
                        'hint' => 'Enter a valid email address.',
                    ],
                    'password' => [
                        'label' => 'Password',
                        'placeholder' => 'Enter your password',
                        'hint' => 'Use 8 to 128 characters.',
                    ],
                    'confirm_password' => [
                        'label' => 'Confirm password',
                        'placeholder' => 'Confirm your password',
                        'hint' => 'Use 8 to 128 characters.',
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
                        'hint' => 'Enter your username or email address.',
                    ],
                    'password' => [
                        'label' => 'Password',
                        'placeholder' => 'Enter your password',
                        'hint' => 'Enter your password.',
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
                        'hint' => 'Enter a valid email address.',
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
                        'hint' => 'Enter a valid email address.',
                    ],
                    'password' => [
                        'label' => 'New password',
                        'placeholder' => 'Enter your new password',
                        'hint' => 'Use 8 to 128 characters.',
                    ],
                    'confirm_password' => [
                        'label' => 'Confirm new password',
                        'placeholder' => 'Confirm your new password',
                        'hint' => 'Use 8 to 128 characters.',
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
        'confirm_password' => [
            'title' => 'Confirm your password',
            'description' => 'Confirm your password to continue.',
        ],
    ],
    'common' => [
        'logout' => 'Logout',
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
