<?php

namespace App\Controllers\Auth;

use DB;
use App\Controllers\Controller;
use App\Helpers\JWTHelper;
use App\Helpers\SessionHelper;

class AuthController extends Controller
{
    /**
     * Create session
     * 
     * @param string|null $next
     *
     * @return RedirectResponse
     */
    public function login($user_where)
    {
        $user = DB::get('users', ['id', 'active [Bool]', 'admin [Bool]'], $user_where);

        if (!$user) {
            return $this->logout('Account not found!');
        }

        if (!$user['active']) {
            return $this->logout('Your account has been deactivated! Contact the administrator');
        }

        $return_to = SessionHelper::get('return_to');

        SessionHelper::destroy();
        SessionHelper::set('id', $user['id']);
        SessionHelper::set('admin', $user['admin']);
        SessionHelper::set('ip', $_SERVER['REMOTE_ADDR']);
        SessionHelper::set('last_activity', time());

        if ($return_to) {
            return $this->redirect($return_to);
        }

        return $this->redirect('/dashboard');
    }

    /**
     * Destroy session
     * 
     * @param string $message optional
     *
     * @return RedirectResponse
     */
    public function logout($message = 'You have been logged out!')
    {
        SessionHelper::destroy();

        if ($message) {
            $jwt = JWTHelper::create('message', [
                'message' => $message
            ]);

            return $this->redirect("/?msg={$jwt}");
        }

        return $this->redirect('/');
    }
}
