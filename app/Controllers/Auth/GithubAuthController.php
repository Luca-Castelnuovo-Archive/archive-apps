<?php

namespace App\Controllers\Auth;

use DB;
use Exception;
use App\Helpers\StateHelper;
use App\Helpers\StringHelper;
use Zend\Diactoros\ServerRequest;
use League\OAuth2\Client\Provider\Github;

class GithubAuthController extends AuthController
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
            'clientId'     => config('auth.github.client_id'),
            'clientSecret' => config('auth.github.client_secret'),
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
            return $this->logout('State is invalid!');
        }

        try {
            $token = $this->provider->getAccessToken('authorization_code', ['code' => $code]);
            $data = $this->provider->getResourceOwner($token);
        } catch (Exception $e) {
            return $this->logout("Error: {$e}");
        }

        $github_id = StringHelper::escape($data->toArray()['id']);

        // Debug
        return $this->redirect("https://example.com/github/{$github_id}");

        $user = DB::get(
            'users',
            [
                'id',
                'admin',
                'captcha_key',
            ],
            [
                'github_id' => $github_id,
            ]
        );

        if (!$user) {
            return $this->logout('Account not found');
        }

        return $this->login($user->id, $user->admin);
    }
}
