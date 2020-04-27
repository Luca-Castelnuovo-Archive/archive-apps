<?php

namespace App\Controllers\Auth;

use DB;
use Exception;
use App\Helpers\CaptchaHelper;
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

        // validate invite code from db

        return $this->respondJson(
            true,
            'Invite code valid',
            ['redirect' => '/auth/register']
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

        return $this->respondJson(
            true,
            'License code valid',
            ['redirect' => '/auth/register']
        );
    }

    /**
     * View register form
     *
     * @return HtmlResponse
     */
    public function registerView()
    {
        $this->respond('register.twig');
    }

    /**
     * Register new user
     *
     * @param ServerRequest $request
     * @return void
     */
    public function register(ServerRequest $request)
    {
        // handle registration

        // register new user
        // set roles from jwt
    }
}
