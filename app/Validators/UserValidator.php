<?php

namespace App\Validators;

use Respect\Validation\Validator as v;

class UserValidator extends ValidatorBase
{
    /**
     * Validate json submission
     *
     * @param object $data
     *
     * @return void
     */
    public static function addLogin($data)
    {
        $v = v::attribute('type', v::oneOf(v::equals('github'), v::equals('google'), v::equals('email')))
            ->attribute('id', v::oneOf(v::email(), v::number()));

        ValidatorBase::validate($v, $data);
    }

    /**
     * Validate json submission
     *
     * @param object $data
     *
     * @return void
     */
    public static function removeLogin($data)
    {
        $v = v::attribute('type', v::oneOf(v::equals('github'), v::equals('google'), v::equals('email')));

        ValidatorBase::validate($v, $data);
    }
}
