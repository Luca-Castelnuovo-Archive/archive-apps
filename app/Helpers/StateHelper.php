<?php

namespace App\Helpers;

class StateHelper
{
    /**
     * Set state
     * 
     * @return string
     */
    public static function set()
    {
        $state = StringHelper::random();

        return SessionHelper::set('state', $state);
    }

    /**
     * Validate $provided_state
     *
     * @param string $provided_state
     * 
     * @return bool
     */
    public static function valid($provided_state)
    {
        if ($provided_state !== SessionHelper::get('state')) {
            return false;
        }

        SessionHelper::unset('state');

        return true;
    }
}
