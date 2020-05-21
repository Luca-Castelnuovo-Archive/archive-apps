<?php

return [
    'session_expires' => 1800, // 30min
    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET')
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET')
    ],
    'gumroad' => [
        'access_token' => env('GUMROAD_ACCESS_TOKEN')
    ]
];
