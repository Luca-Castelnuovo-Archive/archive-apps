<?php

use App\Helpers\ArrayHelper;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

function config($key, $fallback = null)
{
    static $config;

    if (is_null($config)) {
        $config = [
            'auth' => [
                'session_expires' => 1800, // 30min
                'github' => [
                    'client_id' => env('GITHUB_CLIENT_ID'),
                    'client_secret' => env('GITHUB_CLIENT_SECRET'),
                ],
                'google' => [
                    'client_id' => env('GOOGLE_CLIENT_ID'),
                    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                ],
            ],
            'analytics' => [
                'enabled' => false,
                'domainId' => '',
                'options' => '{ "localhost": false, "detailed": true }'
            ],
            'app' => [
                'url' => env('APP_URL'),
            ],
            'captcha' => [
                'frontend_class' => 'h-captcha',
                'frontend_endpoint' => 'https://hcaptcha.com/1/api.js',
                'endpoint' => 'https://hcaptcha.com/siteverify',
                'site_key' => env('CAPTCHA_SITE_KEY'),
                'secret_key' => env('CAPTCHA_SECRET_KEY')
            ],
            'database' => [
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD')
            ],
            'gumroad' => [
                'access_token' => env('GUMROAD_ACCESS_TOKEN')
            ],
            'jwt' => [
                'algorithm' => 'RS256',
                'private_key' => str_replace('||||', PHP_EOL, env('JWT_PRIVATE_KEY')),
                'public_key' => str_replace('||||', PHP_EOL, env('JWT_PUBLIC_KEY')),
                'iss' => env('APP_URL'),
                'message' => 15, // 15seconds
                'emailLogin' => 300, // 5minutes
                'register' => 3600, // 1hour
                'invite' => 604800, // 1week
                'auth' => 600, // 10minutes - external auth
            ],
            'mail' => [
                'endpoint' => 'https://mailjs.lucacastelnuovo.nl/submit',
                'access_token' => env('MAIL_KEY'),
                'invite' => [
                    'subject' => '[apps.lucacastelnuovo.nl] You have been invited',
                    'preheader' => 'You have been invited to use apps.lucacastelnuovo.nl.',
                    'message' => 'You have been invited to use apps.lucacastelnuovo.nl. Use the button below to set up your account and get started',
                    'btn_text' => 'Set up account',
                ],
                'emailLogin' => [
                    'subject' => '[apps.lucacastelnuovo.nl] Login',
                    'preheader' => 'Click link to log in to apps.lucacastelnuovo.nl.',
                    'message' => 'You have requested a login link of apps.lucacastelnuovo.nl. Use the button below to log in and continue:',
                    'btn_text' => 'Log in to app',
                ],
            ],
        ];
    }

    return ArrayHelper::get($config, $key, $fallback);
}
