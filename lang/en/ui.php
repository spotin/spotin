<?php

declare(strict_types=1);

return [
    'welcome' => [
        'title' => 'Welcome',
        'first_paragraph' => 'First paragraph text.',
        'second_paragraph' => 'Second paragraph text.',
        'button' => 'Click me',
    ],
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
                        'label' => 'Email',
                        'placeholder' => 'Enter your email',
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
            'dont_have_an_account' => "Don't have an account?",
            'register' => 'Register',
        ],
    ],
    'common' => [
        'logout' => 'Logout',
        'version' => 'Version :version',
        'fill_the_form' => 'Please fill out the form below to continue.',
    ],
];
