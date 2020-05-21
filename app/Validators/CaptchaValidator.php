<?php

namespace App\Validators;

use CQ\Validators\Validator;
use Respect\Validation\Validator as v;

class CaptchaValidator extends Validator
{
    /**
     * Validate json submission
     *
     * @param object $data
     *
     * @return void
     */
    public static function submit($data)
    {
        $v = v::attribute('h-captcha-response', v::stringType());

        self::validate($v, $data);
    }
}
