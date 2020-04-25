<?php

namespace App\Controllers\Auth;

use Exception;
use App\Helpers\SessionHelper;
use App\Helpers\StringHelper;
use Zend\Diactoros\ServerRequest;
use League\OAuth2\Client\Provider\Github;

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
        $this->provider = new Github([
            'clientId'     => config('auth.client_id'),
            'clientSecret' => config('auth.client_secret'),
            'redirectUri'  => config('auth.redirect_url'),
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
        SessionHelper::set('state', $this->provider->getState());

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

        if (empty($state) || ($state !== SessionHelper::get('state'))) {
            return $this->logout('Provided state is invalid!');
        }

        try {
            $token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);
            $user_id = StringHelper::escape($this->provider->getResourceOwner($token)->getNickname());

            return $this->login($user_id);
        } catch (Exception $e) {
            return $this->logout("Error: {$e}");
        }

        return $this->redirect('/dashboard');
    }
}
