<?php

namespace App\Controllers\Auth;

use DB;
use Exception;
use App\Helpers\CaptchaHelper;
use App\Helpers\JWTHelper;
use App\Helpers\MailHelper;
use App\Helpers\StateHelper;
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
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        try {
            CaptchaHelper::validate($request->data->{'h-captcha-response'});
        } catch (Exception $e) {
            return $this->respondJson(
                false,
                'Please complete captcha',
                json_decode($e->getMessage()),
                422
            );
        }

        // if (!DB::has('users', ['email' => $request->data->email])) {
        //     return $this->respondJson(
        //         false,
        //         'User account not found'
        //     );
        // }

        $app_url = config('app.url');
        $state = StateHelper::set();
        $code = JWTHelper::create(
            'invite',
            [
                'sub' => $request->data->email,
                'state' => $state
            ]
        );
        $url = "{$app_url}/auth/email/callback?code={$code}";

        try {
            MailHelper::send('emailLogin', $request->data->email, 'User', $url);
        } catch (Exception $e) {
            return $this->respondJson(
                false,
                'Login link could not be sent',
                json_decode($e->getMessage()),
                500
            );
        }

        return $this->respondJson(
            true,
            'Login link has been sent to your inbox'
        );
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
        $code = $request->getQueryParams()['code'];

        try {
            $jwt = JWTHelper::valid('invite', $code);
        } catch (Exception $e) {
            return $this->logout($e->getMessage());
        }

        if (!StateHelper::valid($jwt->state)) {
            return $this->logout('Please open link on the same device that requested the login');
        }

        // Debug
        return $this->redirect("https://example.com/email/{$jwt->sub}");

        $user = DB::get(
            'users',
            [
                'id',
                'admin',
                'captcha_key',
            ],
            [
                'email' => $jwt->sub,
            ]
        );

        return $this->login($user->id, $user->admin);
    }
}
