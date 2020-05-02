<?php

namespace App\Validators;

use Respect\Validation\Validator as v;

class AppValidator extends ValidatorBase
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
        $v = v::attribute('gumroad_id', v::alnum())
            ->attribute('name', v::alnum('.', '-', ' '))
            ->attribute('url', v::url());

        ValidatorBase::validate($v, $data);
    }

    /**
     * Validate json submission
     *
     * @param object $data
     *
     * @return void
     */
    public static function update($data)
    {
        // TODO: make optional
        $v = v::attribute('gumroad_id', v::alnum())
            ->attribute('name', v::alnum('.', '-', ' '))
            ->attribute('url', v::url());

        ValidatorBase::validate($v, $data);
    }
}
