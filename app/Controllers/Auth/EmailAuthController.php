<?php

namespace App\Controllers\Auth;

use Exception;
use CQ\DB\DB;
use CQ\Config\Config;
use CQ\Helpers\JWT;
use CQ\Helpers\State;
use App\Helpers\Mail;
use App\Validators\EmailAuthValidator;

class EmailAuthController extends AuthController
{
    /**
     * Request for email
     *
     * @param object $request
     * 
     * @return Json;
     */
    public function request($request)
    {
        try {
            EmailAuthValidator::request($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        if (!DB::has('users', ['email' => $request->data->email])) {
            return $this->respondJson(
                'Email not found',
                [],
                400
            );
        }

        try {
            $app_url = Config::get('app.url');
            $jwt = JWT::create([
                'type' => 'emailLogin',
                'sub' => $request->data->email,
                'state' => State::set()
            ], Config::get('jwt.emailLogin'));

            Mail::send(
                'emailLogin',
                $request->data->email,
                $request->data->email,
                "{$app_url}/auth/email/callback?code={$jwt}"
            );
        } catch (Exception $e) {
            return $this->respondJson(
                'Login link could not be sent',
                json_decode($e->getMessage()),
                500
            );
        }

        return $this->respondJson('Login link has been sent to your inbox');
    }

    /**
     * Callback for email
     *
     * @param object $request
     * 
     * @return Redirect
     */
    public function callback($request)
    {
        $code = $request->getQueryParams()['code'];

        try {
            $jwt = JWT::valid('emailLogin', $code);
        } catch (Exception $e) {
            return $this->logout('token');
        }

        if (!State::valid($jwt->state)) {
            return $this->logout('stateMail');
        }

        return $this->login(['email' => $jwt->sub]);
    }
}
