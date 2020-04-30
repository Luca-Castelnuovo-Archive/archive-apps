<?php

namespace App\Controllers\Auth;

use DB;
use Exception;
use App\Helpers\CaptchaHelper;
use App\Helpers\JWTHelper;
use App\Helpers\StateHelper;
use App\Validators\RegisterAuthValidator;
use Zend\Diactoros\ServerRequest;

class RegisterAuthController extends AuthController
{
    /**
     * Invite validation
     *
     * @return JsonResponse
     */
    public function invite(ServerRequest $request)
    {
        try {
            RegisterAuthValidator::invite($request->data);
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

        $invite = DB::get('invites', [
            'roles',
            'expires_at',
            'roles'
        ], ['code' => $request->data->invite_code]);
        // TODO: DB::delete('invites', ['code' => $request->data->invite_code]);

        if (!$invite) {
            return $this->respondJson(
                false,
                'Invite code not found'
            );
        }

        if ($invite['expires_at'] < date('Y-m-d H:i:s')) {
            return $this->respondJson(
                false,
                'Invite code has expired'
            );
        }

        $jwt = JWTHelper::create('register', [
            'roles' => $invite['roles'],
            'state' => StateHelper::set()
        ]);

        return $this->respondJson(
            true,
            'Invite code valid',
            ['redirect' => "/auth/register?code={$jwt}"]
        );
    }

    /**
     * License validation
     *
     * @return JsonResponse
     */
    public function license(ServerRequest $request)
    {
        try {
            RegisterAuthValidator::license($request->data);
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

        // validate invite code with gumroad
        $roles = [];

        $jwt = JWTHelper::create('register', [
            'roles' => $roles,
            'state' => StateHelper::set()
        ]);

        return $this->respondJson(
            true,
            'Invite code valid',
            ['redirect' => "/auth/register?code={$jwt}"]
        );
    }

    /**
     * View register form
     * 
     * @param ServerRequest $request
     *
     * @return HtmlResponse
     */
    public function registerView(ServerRequest $request)
    {
        $code = $request->getQueryParams()['code'];

        try {
            $jwt = JWTHelper::valid('register', $code);
        } catch (Exception $e) {
            return $this->logout($e->getMessage());
        }

        if (!StateHelper::valid($jwt->state, false)) {
            return $this->logout('State is invalid');
        }

        return $this->respond('register.twig', [
            'code' => $code,
            'roles' => $jwt->roles
        ]);
    }

    /**
     * Register new user
     *
     * @param ServerRequest $request
     * @return void
     */
    public function register(ServerRequest $request)
    {
        try {
            RegisterAuthValidator::register($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                false,
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        try {
            $jwt = JWTHelper::valid('register', $request->data->code);
        } catch (Exception $e) {
            return $this->logout($e->getMessage());
        }

        if (!StateHelper::valid($jwt->state)) {
            return $this->respondJson(
                false,
                'State is invalid',
                ['redirect' => '/']
            );
        }

        // check if user already exists

        // set roles from $jwt->roles

        // register new user

        return $this->respondJson(
            true,
            'Account Created',
            ['redirect' => '/']
        );
    }
}
