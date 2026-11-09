<?php

declare(strict_types=1);

return [
    'contact' => [
        'subject' => 'New message from :organization',
        'from' => 'From: :name (:email)',
        'organization' => 'Institution: :organization',
        'reply' => 'Reply to this email to answer them.',
    ],
    'invitation' => [
        'subject' => 'You have been invited to join :organization',
        'line' => 'You have been invited to join :organization as :role.',
        'action' => 'View the invitation',
        'expires' => 'This invitation expires on :date.',
        'ignore' => 'If you were not expecting this invitation, you can ignore this email.',
    ],
    'reset_password' => [
        'subject' => 'Reset your password',
        'line' => 'You are receiving this email because we received a password reset request for your account.',
        'action' => 'Reset the password',
        'expires' => 'This password reset link will expire in :count minutes.',
        'ignore' => 'If you did not request a password reset, you can ignore this email.',
    ],
    'verify_email' => [
        'subject' => 'Verify your email address',
        'line' => 'Please click the button below to verify your email address.',
        'action' => 'Verify the email address',
        'ignore' => 'If you did not create an account, you can ignore this email.',
    ],
];
