<?php

namespace App\Controllers\Auth;

use Exception;
use App\Helpers\StateHelper;
use App\Helpers\StringHelper;
use Zend\Diactoros\ServerRequest;
use League\OAuth2\Client\Provider\Google;

class GoogleAuthController extends AuthController
{
    private $provider;

    /**
     * Initialize the OAuth provider
     * 
     * @return void
     */
    public function __construct()
    {
        $this->provider = new Google([
            'clientId'     => config('auth.google.client_id'),
            'clientSecret' => config('auth.google.client_secret'),
            'redirectUri' => config('app.url') . '/auth/google/callback'
        ]);
    }

    /**
     * Redirect to OAuth
     *
     * @return RedirectResponse
     */
    public function request()
    {
        $authUrl = $this->provider->getAuthorizationUrl();
        StateHelper::set($this->provider->getState());

        return $this->redirect($authUrl);
    }

    /**
     * Callback for OAuth
     *
     * @param ServerRequest $request
     * 
     * @return RedirectResponse
     */
    public function callback(ServerRequest $request)
    {
        $state = $request->getQueryParams()['state'];
        $code = $request->getQueryParams()['code'];

        if (!StateHelper::valid($state)) {
            return $this->logout('State is invalid');
        }

        try {
            $token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);
            $data = $this->provider->getResourceOwner($token);
        } catch (Exception $e) {
            return $this->logout("Error: {$e}");
        }

        $google_id = StringHelper::escape($data->toArray()['sub']);

        return $this->login(['google_id' => $google_id]);
    }
}
