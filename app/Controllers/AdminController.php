<?php

namespace App\Controllers;

use DB;
use Exception;
use App\Helpers\MailHelper;
use App\Helpers\SessionHelper;
use App\Helpers\CaptchaHelper;
use Zend\Diactoros\ServerRequest;

class AdminController extends Controller
{
    /**
     * Create Invite
     * Update User
     * Enable/Disable User
     * Enable/Disable admin priv
     */

    /**
     * List users
     *
     * @return HtmlResponse
     */
    public function users()
    {
        // list all users

        $users = [];

        return $this->respond('admin/users.twig', [
            'users' => $users
        ]);
    }

    /**
     * Update user
     *
     * @param ServerRequest $request
     * @param string $id
     * 
     * @return JsonResponse
     */
    public function user(ServerRequest $request, $id)
    {
        // update user

        return $this->respondJson();
    }

    /**
     * List apps
     *
     * @return HtmlResponse
     */
    public function apps()
    {
        // list all users

        $apps = [];

        return $this->respond('admin/apps.twig', [
            'apps' => $apps
        ]);
    }

    /**
     * Update app
     *
     * @param ServerRequest $request
     * @param string $id
     * 
     * @return JsonResponse
     */
    public function app(ServerRequest $request, $id)
    {
        // update user

        return $this->respondJson();
    }
}
