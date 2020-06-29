<?php

namespace App\Validators;

use CQ\Validators\Validator;
use Respect\Validation\Validator as v;

class UserValidator extends Validator
{
    /**
     * Validate json submission.
     *
     * @param object $data
     */
    public static function addLogin($data)
    {
        $v = v::attribute('type', v::oneOf(v::equals('github'), v::equals('google'), v::equals('email')))
            ->attribute('id', v::oneOf(v::email(), v::number()))
        ;

        self::validate($v, $data);
    }

    /**
     * Validate json submission.
     *
     * @param object $data
     */
    public static function removeLogin($data)
    {
        $v = v::attribute('type', v::oneOf(v::equals('github'), v::equals('google'), v::equals('email')));

        self::validate($v, $data);
    }
}
