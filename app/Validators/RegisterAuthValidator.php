<?php

namespace App\Validators;

use Respect\Validation\Validator as v;

class RegisterAuthValidator extends ValidatorBase
{
    /**
     * Validate json submission
     *
     * @param object $data
     *
     * @return void
     */
    public static function invite($data)
    {
        $v = v::attribute('h-captcha-response', v::stringType())
            ->attribute('invite_code', v::stringType());

        ValidatorBase::validate($v, $data);
    }

    /**
     * Validate json submission
     *
     * @param object $data
     *
     * @return void
     */
    public static function license($data)
    {
        $v = v::attribute('h-captcha-response', v::stringType())
            ->attribute('license_code', v::stringType());

        ValidatorBase::validate($v, $data);
    }

    /**
     * Validate json submission
     *
     * @param object $data
     *
     * @return void
     */
    public static function register($data) // TODO: build validation
    {
        $v = v::attribute('code', v::stringType());

        ValidatorBase::validate($v, $data);
    }
}
