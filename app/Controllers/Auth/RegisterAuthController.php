<?php

namespace App\Controllers\Auth;

use DB;
use Exception;
use App\Helpers\CaptchaHelper;
use App\Helpers\JWTHelper;
use App\Helpers\StateHelper;
use App\Validators\RegisterAuthValidator;
use Zend\Diactoros\ServerRequest;
use Ramsey\Uuid\Uuid;

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

        return $this->respond('register.twig', ['code' => $code]);
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

        switch ($request->data->type) {
            case 'github':
                $existing_user = DB::get('users', 'id', ['github_id' => $request->data->github_id]);
                $existing_user_message = 'Github account already registered to an account, please use another github account';
                break;

            case 'google':
                $existing_user = DB::get('users', 'id', ['google_id' => $request->data->google_id]);
                $existing_user_message = 'Google account already registered to an account, please use another google account';
                break;

            case 'email':
                $existing_user = DB::get('users', 'id', ['email' => $request->data->email]);
                $existing_user_message = 'Email already registered to an account, please use another email';
                break;
        }

        if ($existing_user) {
            return $this->respondJson(
                false,
                $existing_user_message
            );
        }

        // if (!StateHelper::valid($jwt->state)) {
        //     return $this->respondJson(
        //         false,
        //         'State is invalid',
        //         ['redirect' => '/']
        //     );
        // }

        // DB::create('users', [
        //     'id' => Uuid::uuid4()->toString(),
        //     'roles' => $jwt->roles,
        //     'email' => $request->data->email ?: null,
        //     'google_id' => $request->data->google_id ?: null,
        //     'github_id' => $request->data->github_id ?: null
        // ]);

        // MAYBE: send welcome mail, if email present

        return $this->respondJson(
            true,
            'Account Created',
            $request->data
            // ['redirect' => '/']
        );
    }
}
