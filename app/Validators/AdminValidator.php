<?php

namespace App\Validators;

use Respect\Validation\Validator as v;

class AdminValidator extends ValidatorBase
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

        ValidatorBase::validate($v, $data);
    }
}
