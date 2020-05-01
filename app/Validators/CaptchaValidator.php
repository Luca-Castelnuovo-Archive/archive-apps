<?php

namespace App\Validators;

use Respect\Validation\Validator as v;

class CaptchaValidator extends ValidatorBase
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
        $v = v::attribute(config('captcha.frontend_class') . '-response', v::stringType());

        ValidatorBase::validate($v, $data);
    }
}
