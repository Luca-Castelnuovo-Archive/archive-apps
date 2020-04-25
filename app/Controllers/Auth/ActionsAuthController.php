<?php

namespace App\Controllers\Auth;

use Zend\Diactoros\ServerRequest;

class ActionsAuthController extends AuthController
{
    /**
     * Handle views
     *
     * @param ServerRequest $request
     * @param string $path
     * 
     * @return HtmlResponse
     */
    public function views(ServerRequest $request, $path)
    {
        switch ($path) {
            case 'activate':
                $template = 'activate.twig';
                break;

            case 'invite':
                $template = 'invite.twig';
                break;

            case 'reset':
                $template = 'reset.twig';
                break;

            case 'resetRequest':
                $template = 'resetRequest.twig';
                break;

            case 'verify':
                $template = 'verify.twig';
                break;

            default:
                $template = 'activate.twig';
                // redirect to 404
                break;
        }

        return $this->respond("auth/${template}", [
            'code' => $request->getQueryParams()['code']
        ]);
    }

    /**
     * Activate user
     *
     * @return RedirectResponse
     */
    public function activate()
    {
        // validate captcha
        // validat activation code

        return $this->logout('Account Activated');
    }

    /**
     * Invite user (existing or new)
     *
     * @return RedirectResponse
     */
    public function invite()
    {
        // validate captcha
        // validate invite code

        // register new user
        // send welcome mail
        // if used email, set email_verified false, gen and send verify link
        // if oauth, set account_id for the corresponding service
        // set roles from jwt

        return $this->logout('Account Created');
    }

    /**
     * Password reset
     *
     * @return RedirectResponse
     */
    public function reset()
    {
        // verify captcha
        // verify update password code

        // update password
        // send mail that password changed

        return $this->logout('Password Updated');
    }

    /**
     * Request password reset
     *
     * @return RedirectResponse
     */
    public function resetRequest()
    {
        // verify captcha

        // lookup user
        // if exists gen token and send
        // if not exists send non-existent user email

        return $this->logout('Reset link sent to inbox');
    }

    /**
     * Verify email
     *
     * @return RedirectResponse
     */
    public function verify()
    {
        // validate captcha
        // validate verification code

        // activate account

        return $this->logout('Email verified');
    }
}
