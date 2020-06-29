<?php

namespace App\Controllers\Auth;

use CQ\Controllers\Controller;
use CQ\DB\DB;
use CQ\Helpers\Session;

class AuthController extends Controller
{
    /**
     * Create session.
     *
     * @param string|null $next
     * @param mixed       $user_where
     *
     * @return Redirect
     */
    public function login($user_where)
    {
        $user = DB::get('users', ['id', 'active [Bool]', 'admin [Bool]'], $user_where);

        if (!$user) {
            return $this->logout('notfound');
        }

        if (!$user['active']) {
            return $this->logout('deactivated');
        }

        $return_to = Session::get('return_to');

        Session::destroy();
        Session::set('id', $user['id']);
        Session::set('admin', $user['admin']);
        Session::set('ip', $_SERVER['REMOTE_ADDR']);
        Session::set('last_activity', time());

        if ($return_to) {
            return $this->redirect($return_to);
        }

        return $this->redirect('/dashboard');
    }

    /**
     * Destroy session.
     *
     * @param string $message optional
     *
     * @return Redirect
     */
    public function logout($message = 'logout')
    {
        Session::destroy();

        if ($message) {
            return $this->redirect("/?msg={$message}");
        }

        return $this->redirect('/');
    }
}
