<?php

namespace App\Controllers;

use Exception;
use App\Helpers\SessionHelper;
use App\Helpers\StringHelper;
use App\Helpers\JWTHelper;
use Zend\Diactoros\ServerRequest;
use League\OAuth2\Client\Provider\Github;

class AuthController extends Controller
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
     * Login start OAuth
     *
     * @return RedirectResponse
     */
    public function login()
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

        // Prevent session fixation
        SessionHelper::destroy();

        try {
            $token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);
            $user_id = StringHelper::escape($this->provider->getResourceOwner($token)->getNickname());

            if (!in_array($user_id, config('auth.allowed_users', []))) {
                return $this->logout('Your account has not been authorized!');
            }

            SessionHelper::set('user_id', $user_id);
            SessionHelper::set('last_activity', time());
            SessionHelper::set('ip', $_SERVER['REMOTE_ADDR']);
        } catch (Exception $e) {
            return $this->logout("Error: {$e}");
        }

        return $this->redirect('/dashboard');
    }

    /**
     * Destroy session
     * 
     * @param string $message optional
     *
     * @return RedirectResponse
     */
    public function logout($message = 'You have been logged out!')
    {
        SessionHelper::destroy();

        if ($message) {
            $message = JWTHelper::create('message', [
                'message' => $message
            ], 5);

            return $this->redirect("/?msg={$message}");
        }

        return $this->redirect('/');
    }
}
