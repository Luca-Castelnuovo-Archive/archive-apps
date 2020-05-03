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
        $v = v::attribute('invite_code', v::alnum()->length(1, 128));

        ValidatorBase::validate($v, $data);
    }

    /**
     * Validate json submission
     *
     * @param object $data
     *
     * @return void
     */
    public static function register($data)
    {
        $v = v::attribute('code', v::stringType())
            ->attribute('type', v::oneOf(v::equals('github'), v::equals('google'), v::equals('email')))
            ->attribute('github', v::optional(v::stringType()->length(1, 255)))
            ->attribute('google', v::optional(v::stringType()->length(1, 255)))
            ->attribute('email', v::optional(v::email()->length(1, 255)));

        ValidatorBase::validate($v, $data);
    }
}
