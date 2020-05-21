<?php

namespace App\Validators;

use CQ\Validators\Validator;
use Respect\Validation\Validator as v;

class AdminValidator extends Validator
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
        $v = v::attribute('email', v::email());

        self::validate($v, $data);
    }
}
