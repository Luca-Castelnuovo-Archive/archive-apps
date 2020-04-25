<?php

use App\Helpers\ArrayHelper;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

function config($key, $fallback = null)
{
    static $config;

    if (is_null($config)) {
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
                'algorithm' => 'HS256',
                'public_key' => env('JWT_PUBLIC_KEY'),
                'private_key' => env('JWT_PRIVATE_KEY'),
                'iss' => env('JWT_ISS'),
                'invite' => [ // used to invite new users or grant additional privileges
                    'aud' => env('JWT_ISS'),
                    'exp' => 604800, // 1 week
                    'type' => 'invite'
                ],
                'email' => [
                    'aud' => env('JWT_ISS'),
                    'exp' => 86400, // 1 day
                    'type' => 'verify_email'
                ],
                'active' => [
                    'aud' => env('JWT_ISS'),
                    'exp' => 604800, // 1 week
                    'type' => 'invite'
                ],
                'active' => [
                    'aud' => env('JWT_ISS'),
                    'exp' => 604800, // 1 week
                    'type' => 'invite'
                ],
                'auth' => [
                    'aud' => env('JWT_ISS'),
                    'exp' => 604800, // 1 week
                    'type' => 'invite'
                ],
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
