<?php

namespace App\Helpers;

class AuthHelper
{
    /**
     * Check if session active
     * 
     * @return bool
     */
    public static function valid()
    {
        $id_not_empty = SessionHelper::get('id');
        $ip_match = SessionHelper::get('ip') === $_SERVER['REMOTE_ADDR'];
        $session_valid = time() - SessionHelper::get('last_activity') < config('auth.session_expires');

        if ($id_not_empty && $ip_match && $session_valid) {
            SessionHelper::set('last_activity', time());

            return true;
        }

        return false;
    }
}
