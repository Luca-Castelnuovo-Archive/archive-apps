<?php

namespace App\Helpers;

use CQ\Config\Config;
use CQ\Helpers\Request;
use CQ\Helpers\Session;

class Auth
{
    /**
     * Check if session active
     * 
     * @return bool
     */
    public static function valid()
    {
        $id_not_empty = Session::get('id');
        $ip_match = Session::get('ip') === Request::ip();
        $session_valid = time() - Session::get('last_activity') < Config::get('auth.session_expires');

        if ($id_not_empty && $ip_match && $session_valid) {
            Session::set('last_activity', time());

            return true;
        }

        return false;
    }
}
