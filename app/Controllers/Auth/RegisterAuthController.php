<?php

namespace App\Controllers\Auth;

use Exception;
use CQ\DB\DB;
use CQ\Config\Config;
use CQ\Helpers\JWT;
use CQ\Helpers\UUID;
use CQ\Helpers\State;
use App\Validators\RegisterAuthValidator;

class RegisterAuthController extends AuthController
{
    /**
     * Invite validation
     * 
     * @param object $request
     *
     * @return Json
     */
    public function invite($request)
    {
        try {
            RegisterAuthValidator::invite($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        $invite = DB::get('invites', ['expires_at'], ['code' => $request->data->invite_code]);

        if (!$invite) {
            return $this->respondJson(
                'Invite not found',
                [],
                400
            );
        }

        if ($invite['expires_at'] < date('Y-m-d H:i:s')) {
            return $this->respondJson(
                'Invite has expired',
                [],
                400
            );
        }

        DB::delete('invites', ['code' => $request->data->invite_code]);

        $jwt = JWT::create([
            'type' => 'register',
            'state' => State::set()
        ], Config::get('jwt.register'));

        return $this->respondJson(
            'Invite valid',
            ['redirect' => "/auth/register?code={$jwt}"]
        );
    }

    /**
     * View register form
     * 
     * @param object $request
     *
     * @return Html
     */
    public function registerView($request)
    {
        $code = $request->getQueryParams()['code'];

        try {
            $jwt = JWT::valid('register', $code);
        } catch (Exception $e) {
            return $this->logout($e->getMessage());
        }

        if (!State::valid($jwt->state, false)) {
            return $this->logout('state');
        }

        return $this->respond('auth/register.twig', ['code' => $code]);
    }

    /**
     * Register new user
     *
     * @param object $request
     * 
     * @return Redirect|Json
     */
    public function register($request)
    {
        try {
            RegisterAuthValidator::register($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        $type = $request->data->type;
        $data =  $request->data->{$type};

        if (!$data) {
            return $this->respondJson(
                'Provided data was malformed',
                [],
                422
            );
        }

        try {
            $jwt = JWT::valid('register', $request->data->code);
        } catch (Exception $e) {
            return $this->logout($e->getMessage());
        }

        if (DB::has('users', [$type => $data])) {
            return $this->respondJson(
                "This {$type} account is already used, please use another",
                [],
                400
            );
        }

        if (!State::valid($jwt->state)) {
            return $this->respondJson(
                'State is invalid',
                ['redirect' => '/'],
                400
            );
        }

        DB::create('users', [
            'id' => UUID::v6(),
            $type => $data
        ]);

        return $this->respondJson(
            'Account Created',
            ['redirect' => '/?msg=register']
        );
    }
}
