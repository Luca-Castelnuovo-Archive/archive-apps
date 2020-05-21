<?php

return [
    'algorithm' => 'RS256',
    'private_key' => str_replace('||||', PHP_EOL, env('JWT_PRIVATE_KEY')),
    'public_key' => str_replace('||||', PHP_EOL, env('JWT_PUBLIC_KEY')),
    'iss' => env('APP_URL'),
    'emailLogin' => 300, // 5minutes
    'register' => 3600, // 1hour
    'auth' => 600 // 10minutes - external auth
];
