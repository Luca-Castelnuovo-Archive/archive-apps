<?php

return [
    'access_token' => env('MAIL_KEY'),
    'invite' => [
        'subject' => '[apps.lucacastelnuovo.nl] You have been invited',
        'preheader' => 'You have been invited to use apps.lucacastelnuovo.nl.',
        'message' => 'You have been invited to use apps.lucacastelnuovo.nl. Use the button below to set up your account and get started',
        'btn_text' => 'Set up account'
    ],
    'emailLogin' => [
        'subject' => '[apps.lucacastelnuovo.nl] Login',
        'preheader' => 'Click link to log in to apps.lucacastelnuovo.nl.',
        'message' => 'You have requested a login link of apps.lucacastelnuovo.nl. Use the button below to log in and continue:',
        'btn_text' => 'Log in to app'
    ]
];
