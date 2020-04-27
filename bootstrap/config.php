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
                'session_expires' => 1800, // 30 min
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
                'algorithm' => 'RS256',
                // 'private_key' => env('JWT_PUBLIC_KEY'),
                'private_key' => <<<EOD
                -----BEGIN RSA PRIVATE KEY-----
                MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
                vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
                5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
                AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
                bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
                Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
                cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
                5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
                ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
                k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
                qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
                eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
                B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
                -----END RSA PRIVATE KEY-----
                EOD,
                // 'public_key' => env('JWT_PRIVATE_KEY'),
                'public_key' => <<<EOD
                -----BEGIN PUBLIC KEY-----
                MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
                4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
                0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
                ehde/zUxo6UvS7UrBQIDAQAB
                -----END PUBLIC KEY-----
                EOD,
                'iss' => env('APP_URL'),
                'message' => 5, // 5seconds
                'invite' => 604800, // 1week
                'emailLogin' => 300, // 5minutes
                'emailVerify' => 86400, // 1day
                'auth' => 604800, // 1week
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
            ],
        ];
    }

    return ArrayHelper::get($config, $key, $fallback);
}
