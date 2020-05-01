<?php

namespace App\Controllers\Auth;

use DB;
use Exception;
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

        $invite = DB::get('invites', ['expires_at'], ['code' => $request->data->invite_code]);

        if (!$invite) {
            return $this->respondJson(
                false,
                'Invite code not found',
                [],
                400
            );
        }

        if ($invite['expires_at'] < date('Y-m-d H:i:s')) {
            return $this->respondJson(
                false,
                'Invite code has expired',
                [],
                400
            );
        }

        DB::delete('invites', ['code' => $request->data->invite_code]);

        $jwt = JWTHelper::create('register', [
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

        // TODO: validate invite code with gumroad
        return $this->respondJson(
            false,
            'License code not found'
        );

        $jwt = JWTHelper::create('register', ['state' => StateHelper::set()]);

        return $this->respondJson(
            true,
            'License code valid',
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

        return $this->respond('auth/register.twig', ['code' => $code]);
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

        $type = $request->data->type;
        $data =  $request->data->{$type};

        if (!$data) {
            return $this->respondJson(
                false,
                'Provided data was malformed',
                [],
                422
            );
        }

        try {
            $jwt = JWTHelper::valid('register', $request->data->code);
        } catch (Exception $e) {
            return $this->logout($e->getMessage());
        }

        if (DB::has('users', [$type => $data])) {
            return $this->respondJson(
                false,
                "This {$type} account is already used, please use another",
                [],
                400
            );
        }

        if (!StateHelper::valid($jwt->state)) {
            return $this->respondJson(
                false,
                'State is invalid',
                ['redirect' => '/'],
                400
            );
        }

        DB::create('users', [
            'id' => Uuid::uuid4()->toString(),
            $type => $data
        ]);

        $jwt = JWTHelper::create('message', ['message' => 'You can now login']);
        return $this->respondJson(
            true,
            'Account Created',
            ['redirect' => "/?msg={$jwt}"]
        );
    }
}
