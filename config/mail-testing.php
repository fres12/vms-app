<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Configuration for Testing
    |--------------------------------------------------------------------------
    |
    | This file contains the email configuration for testing purposes.
    | You can use this with Gmail SMTP or other email providers.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'your-email@gmail.com'),
        'name' => env('MAIL_FROM_NAME', 'VMS App'),
    ],
]; 