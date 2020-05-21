<?php

namespace App\Validators;

use CQ\Validators\Validator;
use Respect\Validation\Validator as v;

class EmailAuthValidator extends Validator
{
    /**
     * Validate json submission
     *
     * @param object $data
     *
     * @return void
     */
    public static function request($data)
    {
        $v = v::attribute('email', v::email()->length(1, 255));

        self::validate($v, $data);
    }
}
