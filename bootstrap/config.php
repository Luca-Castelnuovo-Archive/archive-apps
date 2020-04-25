<?php

use App\Helpers\ArrayHelper;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

function config($key, $fallback = null)
{
    static $config;

    if (is_null($config)) {
        $configExternal = json_decode(file_get_contents(env('EXTERNAL_CONFIG')));
        $config = [
            'links' => [
                'captcha' => 'https://www.google.com/recaptcha/api/siteverify',
                'docs' => 'https://ltcastelnuovo.gitbook.io/mailjs/',
                'sdk' => 'https://www.npmjs.com/package/mailjs-sdk',
                'template' => 'https://beefree.io/editor/?template=empty'
            ],
            'analytics' => [
                'enabled' => true,
                'domainId' => '751563b2-769f-441f-bfab-b3f2c099ccc8',
                'options' => '{ "localhost": false, "detailed": true }'
            ],
            'auth' => [
                'client_id' => env('GITHUB_CIENT_ID'),
                'client_secret' => env('GITHUB_CLIENT_SECRET'),
                'redirect_url' => env('GITHUB_REDIRECT'),
                'allowed_users' => $configExternal->allowed_users,
                'session_expires' => 1800 // 30 min
            ],
            'cors' => [
                'allow_origins' => ['*'],
                'allow_headers' => ['Authorization', 'Content-Type'],
                'allow_methods' => ['HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']
            ],
            'database' => [
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD')
            ],
            'hmac' => [
                'algorithm' => 'sha256',
                'secret' => env('APP_KEY')
            ],
            'jwt' => [
                'iss' => env('APP_URL'),
                'ttl' => 31536000, // 1 year
                'algorithm' => 'HS256',
                'secret' => env('APP_KEY')
            ],
            'smtp' => [
                'host' => env('SMTP_HOST'),
                'port' => env('SMTP_PORT'),
                'username' => env('SMTP_USER'),
                'password' => env('SMTP_PASSWORD'),
                'fromName' => 'Luca Castelnuovo'
            ]
        ];
    }

    return ArrayHelper::get($config, $key, $fallback);
}
