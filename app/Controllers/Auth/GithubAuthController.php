<?php

namespace App\Controllers\Auth;

use Exception;
use App\Helpers\StateHelper;
use App\Helpers\StringHelper;
use Zend\Diactoros\ServerRequest;
use League\OAuth2\Client\Provider\Github;

class GithubAuthController extends AuthController
{
    /**
     * Initialize the OAuth provider
     * 
     * @param bool $popup
     * 
     * @return Github
     */
    private function provider($popup = false)
    {
        $queryString = $popup ? '?popup=1' : '';

        return new Github([
            'clientId'     => config('auth.github.client_id'),
            'clientSecret' => config('auth.github.client_secret'),
            'redirectUri' => config('app.url') . '/auth/github/callback' . $queryString
        ]);
    }

    /**
     * Redirect to OAuth
     *
     * @param ServerRequest $request
     * 
     * @return RedirectResponse
     */
    public function request(ServerRequest $request)
    {
        $popup = $request->getQueryParams()['popup'];
        $provider = $this->provider($popup);

        $authUrl = $provider->getAuthorizationUrl();

        // State isn't checked when in popup mode because
        // the /auth/register is only available by an already checked state
        if (!$popup) {
            StateHelper::set($provider->getState());
        }

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
        $popup = $request->getQueryParams()['popup'];
        $state = $request->getQueryParams()['state'];
        $code = $request->getQueryParams()['code'];

        if (!$popup && !StateHelper::valid($state)) {
            return $this->logout('State is invalid!');
        }

        try {
            $provider = $this->provider($popup);
            $token = $provider->getAccessToken('authorization_code', ['code' => $code]);
            $data = $provider->getResourceOwner($token);
            $id = StringHelper::escape($data->toArray()['id']);
        } catch (Exception $e) {
            return $this->logout("Error: {$e}");
        }

        if ($popup) {
            return $this->respond('popup.twig', ['id' => $id]);
        }

        return $this->login(['github' => $id]);
    }
}
