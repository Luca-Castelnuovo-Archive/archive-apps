<?php

namespace App\Controllers;

use DB;
use Exception;
use App\Helpers\MailHelper;
use App\Helpers\StringHelper;
use App\Validators\AdminValidator;
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
        if (!$this->isUserAdmin()) {
            return $this->respondJson(
                'Access Denied',
                [],
                403
            );
        }

        try {
            AdminValidator::invite($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        $code = StringHelper::random();

        DB::create(
            'invites',
            [
                'code' => $code,
                'expires_at' => date("Y-m-d H:i:s", (strtotime(date('Y-m-d H:i:s')) + config('jwt.invite')))
            ]
        );

        try {
            $app_url = config('app.url');
            MailHelper::send(
                'invite',
                $request->data->email,
                $request->data->email,
                "{$app_url}/?invite={$code}"
            );
        } catch (Exception $e) {
            return $this->respondJson(
                'Invite link could not be sent',
                json_decode($e->getMessage()),
                500
            );
        }

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
        if (!$this->isUserAdmin()) {
            return $this->respondJson(
                'Access Denied',
                [],
                403
            );
        }

        $user = DB::get('users', ['active'], ['id' => $id]);

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
