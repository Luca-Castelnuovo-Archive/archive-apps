<?php

return [
    'algorithm' => 'RS256',
    'private_key' => str_replace('||||', PHP_EOL, getenv('JWT_PRIVATE_KEY')),
    'public_key' => str_replace('||||', PHP_EOL, getenv('JWT_PUBLIC_KEY')),
    'iss' => getenv('APP_URL'),
    'emailLogin' => 300, // 5minutes
    'register' => 3600, // 1hour
    'invite' => 604800, // 1week
    'auth' => 600, // 10minutes - external auth
];
