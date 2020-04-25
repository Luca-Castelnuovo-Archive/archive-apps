<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Helpers\SessionHelper;
use App\Helpers\JWTHelper;

class AuthController extends Controller
{
    /**
     * Create session
     * 
     * @param string $user_id
     * @param bool $admin
     *
     * @return RedirectResponse
     */
    public function login($user_id, $admin = false)
    {
        // Prevent session fixation
        SessionHelper::destroy();

        SessionHelper::set('user_id', $user_id);
        SessionHelper::set('is_admin', $admin);
        SessionHelper::set('last_activity', time());
        SessionHelper::set('ip', $_SERVER['REMOTE_ADDR']);

        return $this->redirect('/user/dashboard');
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
        // Prevent session fixation
        SessionHelper::destroy();

        if ($message) {
            $message = JWTHelper::create('message', [
                'message' => $message
            ], 5);

            return $this->redirect("/?msg={$message}");
        }

        return $this->redirect('/');
    }
}
