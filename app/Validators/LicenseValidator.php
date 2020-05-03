<?php

namespace App\Validators;

use Respect\Validation\Validator as v;

class LicenseValidator extends ValidatorBase
{
    /**
     * Validate json submission
     *
     * @param object $data
     *
     * @return void
     */
    public static function create($data)
    {
        $v = v::attribute('license', v::alnum('-'))
            ->attribute('id', v::alnum());

        ValidatorBase::validate($v, $data);
    }

    /**
     * Validate json submission
     *
     * @param object $data
     *
     * @return void
     */
    public static function remove($data)
    {
        $v = v::attribute('license', v::alnum('-'));

        ValidatorBase::validate($v, $data);
    }
}
