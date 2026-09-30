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
        'sign_up' => [
            'title' => 'Sign up',
            'description' => 'Create a new account',
            'form' => [
                'fields' => [
                    'username' => [
                        'label' => 'Username',
                        'placeholder' => 'Enter your username',
                    ],
                    'name' => [
                        'label' => 'Name',
                        'placeholder' => 'Enter your full name',
                    ],
                    'email' => [
                        'label' => 'Email',
                        'placeholder' => 'Enter your email',
                    ],
                    'password' => [
                        'label' => 'Password',
                        'placeholder' => 'Enter your password',
                    ],
                    'confirm_password' => [
                        'label' => 'Confirm password',
                        'placeholder' => 'Confirm your password',
                    ],
                ],
                'actions' => [
                    'submit' => 'Sign up',
                ],
            ],
            'already_have_an_account' => 'Already have an account?',
            'sign_in' => 'Sign in',
        ],
        'sign_in' => [
            'title' => 'Sign in',
            'description' => 'Access your account',
            'form' => [
                'fields' => [
                    'email' => [
                        'label' => 'Email',
                        'placeholder' => 'Enter your email',
                    ],
                    'password' => [
                        'label' => 'Password',
                        'placeholder' => 'Enter your password',
                    ],
                ],
                'actions' => [
                    'submit' => 'Sign in',
                ],
            ],
            'remember_me' => 'Remember me',
            'forgot_your_password' => 'Forgot your password?',
            'dont_have_an_account' => "Don't have an account?",
            'sign_up' => 'Sign up',
        ],
    ],
    'common' => [
        'version' => 'Version :version',
        'required_fields' => 'All fields marked with an asterisk (*) are required.',
    ],
];
