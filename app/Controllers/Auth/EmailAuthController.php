<?php

namespace App\Controllers\Auth;

use DB;
use Exception;
use App\Helpers\SessionHelper;
use App\Helpers\StringHelper;
use App\Helpers\CaptchaHelper;
use App\Helpers\JWTHelper;
use App\Helpers\MailHelper;
use App\Validators\EmailAuthValidator;
use Zend\Diactoros\ServerRequest;

class EmailAuthController extends AuthController
{
    /**
     * Request for email
     *
     * @param ServerRequest $request
     * 
     * @return JsonResponse;
     */
    public function request(ServerRequest $request)
    {
        try {
            EmailAuthValidator::request($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                false,
                'Provided data was misformed',
                json_decode($e->getMessage()),
                422
            );
        }

        if (!CaptchaHelper::validate($request->data['h-captcha-response'])) {
            return $this->respondJson(
                false,
                'Please complete captcha',
                [],
                422
            );
        }

        if (!DB::has('users', ['email' => $request->data->email])) {
            return $this->respondJson(
                false,
                'User account not found'
            );
        }


        // create magic login link

        $app_url = config('app.url');
        $code = JWTHelper::create(
            'invite',
            [
                'sub' => $request->data->email
            ]
        );
        $url = "{$app_url}/auth/email/callback?code={$code}";
        $mailSuccess = MailHelper::send(
            'emailLogin',
            $request->data->email,
            'User',
            $url
        );

        if (!$mailSuccess) {
            return $this->respondJson(
                false,
                'Login link could not be sent'
            );
        }

        return $this->respondJson(
            true,
            'Login link has been sent to your inbox'
        );
    }

    /**
     * Verify user email
     *
     * @param ServerRequest $request
     * 
     * @return JsonResponse;
     */
    public function verify(ServerRequest $request)
    {
        // validate captcha
        // validate token
        // activate user
        // send magic login link

        // return nice message

        return $this->respondJson();
    }

    /**
     * Callback for email
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
