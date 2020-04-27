<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Helpers\AuthHelper;
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
        AuthHelper::login($user_id, $admin);

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
        AuthHelper::logout();

        if ($message) {
            $message = JWTHelper::create('message', [
                'message' => $message
            ], 5);

            return $this->redirect("/?msg={$message}");
        }

        return $this->redirect('/');
    }
}
