<?php

namespace App\Helpers;

class AuthHelper
{
    /**
     * Create session
     *
     * @param string $name
     * @param mixed $data
     * 
     * @return null
     */
    public static function login($user_id, $admin = false)
    {
        SessionHelper::destroy();

        SessionHelper::set('user_id', $user_id);
        SessionHelper::set('is_admin', $admin);
        SessionHelper::set('last_activity', time());
        SessionHelper::set('ip', $_SERVER['REMOTE_ADDR']);
    }

    /**
     * Destroy session
     *
     * 
     * @return null
     */
    public static function logout()
    {
        SessionHelper::destroy();
    }

    /**
     * Check if session active
     *
     * @param string $haystack
     * @param string $needle
     * 
     * @return bool
     */
    public static function valid()
    {
        $ip_match = SessionHelper::get('ip') === $_SERVER['REMOTE_ADDR'];
        $session_valid = time() - SessionHelper::get('last_activity') < config('auth.session_expires');

        SessionHelper::set('last_activity', time());

        return $ip_match && $session_valid;
    }
}
