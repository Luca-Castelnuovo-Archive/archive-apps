<?php

return [
    'session_expires' => 1800, // 30min
    'github' => [
        'client_id' => getenv('GITHUB_CLIENT_ID'),
        'client_secret' => getenv('GITHUB_CLIENT_SECRET')
    ],
    'google' => [
        'client_id' => getenv('GOOGLE_CLIENT_ID'),
        'client_secret' => getenv('GOOGLE_CLIENT_SECRET')
    ],
    'gumroad' => [
        'access_token' => getenv('GUMROAD_ACCESS_TOKEN')
    ]
];
