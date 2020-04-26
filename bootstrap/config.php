<?php

use App\Helpers\ArrayHelper;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

function config($key, $fallback = null)
{
    static $config;

    if (is_null($config)) {
        $config = [
            'analytics' => [
                'enabled' => false,
                'domainId' => '',
                'options' => '{ "localhost": false, "detailed": true }'
            ],
            'captcha' => [
                'frontend_class' => 'h-captcha',
                'frontend_endpoint' => 'https://hcaptcha.com/1/api.js',
                'endpoint' => 'https://hcaptcha.com/siteverify',
                'site_key' => env('CAPTCHA_SITE_KEY'),
                'secret_key' => env('CAPTCHA_SECRET_KEY')
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
            'jwt' => [
                'algorithm' => 'HS256',
                'public_key' => env('JWT_PUBLIC_KEY'),
                'private_key' => env('JWT_PRIVATE_KEY'),
                'iss' => env('JWT_ISS'),
                'login' => [ // used for email magiclink
                    'aud' => env('JWT_ISS'),
                    'exp' => 300, // 5 minutes
                    'type' => 'login'
                ],
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
                'reset' => [
                    'aud' => env('JWT_ISS'),
                    'exp' => 604800, // 1 week
                    'type' => 'reset'
                ],
                'auth' => [
                    'aud' => env('JWT_ISS'),
                    'exp' => 604800, // 1 week
                    'type' => 'invite'
                ],
            ],
            'mail' => [
                'endpoint' => 'https://mailjs.lucacastelnuovo.nl/submit',
                'access_token' => env('MAIL_KEY'),
                'invite' => [
                    'subject' => '[auth] You have been invited',
                    'preheader' => 'You have been invited to use auth.lucacastelnuovo.nl.',
                    'message' => 'You have created an account on auth.lucacastelnuovo.nl. Use the button below to verify your account and get started:',
                    'btn_text' => 'Set up account',
                ],
                'emailLogin' => [
                    'subject' => '[auth] Login',
                    'preheader' => 'Click link to log in to auth.lucacastelnuovo.nl.',
                    'message' => 'You have requested a login link ofr auth.lucacastelnuovo.nl. Use the button below to log in and continue:',
                    'btn_text' => 'Log in to app',
                ],
                'emailVerify' => [
                    'subject' => '[auth] Verify your email',
                    'preheader' => 'You have been invited to use auth.lucacastelnuovo.nl.',
                    'message' => 'You have created an account on auth.lucacastelnuovo.nl. Use the button below to verify your account and get started:',
                    'btn_text' => 'Verify Email',
                ],
            ],
            'oauth' => [
                'session_expires' => 1800, // 30 min
                'github' => [
                    'client_id' => env('GITHUB_CIENT_ID'),
                    'client_secret' => env('GITHUB_CLIENT_SECRET'),
                    'redirect_url' => env('GITHUB_REDIRECT'),
                ],
                'google' => [
                    'client_id' => env('GOOGLE_CIENT_ID'),
                    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                    'redirect_url' => env('GOOGLE_REDIRECT'),
                ],
            ],
        ];
    }

    return ArrayHelper::get($config, $key, $fallback);
}
