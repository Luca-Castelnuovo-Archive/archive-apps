<?php

namespace App\Helpers;

class StateHelper
{
    /**
     * Set state
     * 
     * @param string $custom optional
     * 
     * @return string
     */
    public static function set($custom = '')
    {
        $state = $custom ?: StringHelper::random();

        return SessionHelper::set('state', $state);
    }

    /**
     * Validate $provided_state
     *
     * @param string $provided_state
     * @param bool $unset_state optional
     * 
     * @return bool
     */
    public static function valid($provided_state, $unset_state = true)
    {
        $known_state = SessionHelper::get('state');

        if ($unset_state) {
            SessionHelper::unset('state');
        }

        if (!$provided_state) {
            return false;
        }

        return $provided_state === $known_state;
    }
}
