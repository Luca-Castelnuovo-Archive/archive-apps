<?php

namespace App\Controllers;

use DB;
use App\Helpers\MailHelper;
use App\Helpers\SessionHelper;
use Zend\Diactoros\ServerRequest;

class AdminController extends Controller
{
    /**
     * List apps and users
     *
     * @return HtmlResponse
     */
    public function view()
    {
        if (!$this->isUserAdmin()) {
            return $this->redirect('/dashboard');
        }

        $apps = DB::select('apps', [
            'id',
            'gumroad_id',
            'active',
            'name',
            'url',
            'updated_at',
            'created_at'
        ], '*');

        $users = DB::select('users', [
            'id',
            'active',
            'admin',
            'email',
            'google',
            'github',
            'updated_at',
            'created_at'
        ], '*');

        return $this->respond('admin.twig', [
            'apps' => $apps,
            'users' => $users
        ]);
    }

    /**
     * Invite user
     *
     * @param ServerRequest $request
     * @param string $id
     * 
     * @return JsonResponse
     */
    public function invite(ServerRequest $request)
    {
        // validator

        // TODO: create invite token
        // send email

        return $this->respondJson(
            'Invite Sent',
            ['reload' => true]
        );
    }

    /**
     * Update user
     *
     * @param string $id
     * 
     * @return JsonResponse
     */
    public function userToggle($id)
    {
        $user = DB::get('users', ['active'], ['id' => SessionHelper::get('id')]);

        if (!$user) {
            return $this->respondJson(
                'User not found',
                [],
                400
            );
        }

        // TODO: Update DB - active, inactive

        return $this->respondJson(
            'User Updated',
            ['reload' => true]
        );
    }
}
