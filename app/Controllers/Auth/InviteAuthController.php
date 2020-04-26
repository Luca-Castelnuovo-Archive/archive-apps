<?php

namespace App\Controllers\Auth;

use Zend\Diactoros\ServerRequest;

class InviteAuthController extends AuthController
{
    /**
     * Handle views
     *
     * @param ServerRequest $request
     * @param string $path
     * 
     * @return HtmlResponse
     */
    public function views(ServerRequest $request)
    {
        return $this->respond("auth/", [
            'code' => $request->getQueryParams()['code']
        ]);
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
}
