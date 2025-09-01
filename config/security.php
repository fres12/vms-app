<?php

return [
    'login' => [
        'max_attempts' => env('LOGIN_MAX_ATTEMPTS', 5),
        'lockout_minutes' => env('LOGIN_LOCKOUT_MINUTES', 15),
        'decay_minutes' => env('LOGIN_DECAY_MINUTES', 5),
    ],
    
    'input' => [
        'max_length' => env('INPUT_MAX_LENGTH', 255),
        'sanitize_html' => env('SANITIZE_HTML', true),
    ],
]; 