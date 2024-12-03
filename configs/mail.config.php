<?php

return [
    'mailgun' => [
        'api_key' => $_ENV['MAILGUN_API_KEY'],
        'domain' => $_ENV['MAILGUN_DOMAIN'],
        'from' => [
            'name' => 'House Agency',
            'email' => $_ENV['MAILGUN_FROM_EMAIL'],
        ],
    ],
    'mail_subjects' => [
        'password_recovery' => [
            'en' => 'Recover your password',
            'de' => 'Passwort wiederherstellen',
        ],
        'user_invitation' => [
            'en' => 'Invitation to collaborate',
            'de' => 'Einladung zur Mitarbeit',
        ],
    ],
];