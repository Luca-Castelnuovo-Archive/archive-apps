<?php

namespace App\Validators;

use CQ\Validators\Validator;
use Respect\Validation\Validator as v;

class LicenseValidator extends Validator
{
    /**
     * Validate json submission.
     *
     * @param object $data
     */
    public static function create($data)
    {
        $v = v::attribute('license', v::alnum('-'))
            ->attribute('id', v::alnum())
        ;

        self::validate($v, $data);
    }

    /**
     * Validate json submission.
     *
     * @param object $data
     */
    public static function remove($data)
    {
        $v = v::attribute('license', v::alnum('-'));

        self::validate($v, $data);
    }
}
