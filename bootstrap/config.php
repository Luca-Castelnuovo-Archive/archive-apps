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
            'jwt' => [
                'algorithm' => 'RS256',
                // 'private_key' => env('JWT_PUBLIC_KEY'), // TODO: import from .env like in wiskundesite
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
                'message' => 15, // 15seconds
                'emailLogin' => 300, // 5minutes
                'register' => 3600, // 1hour
                'invite' => 604800, // 1week
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
