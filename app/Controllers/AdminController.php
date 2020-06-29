<?php

namespace App\Controllers;

use App\Helpers\Mail;
use App\Validators\AdminValidator;
use CQ\Config\Config;
use CQ\Controllers\Controller;
use CQ\DB\DB;
use CQ\Helpers\Session;
use CQ\Helpers\Str;
use Exception;

class AdminController extends Controller
{
    /**
     * List apps and users.
     *
     * @return Html
     */
    public function view()
    {
        $apps = DB::select('apps', [
            'id',
            'active',
            'name',
            'url',
            'updated_at',
            'created_at',
        ], [
            'ORDER' => ['name' => 'ASC'],
        ]);

        $users = DB::select('users', [
            'id',
            'active',
            'admin',
            'email',
            'google',
            'github',
            'updated_at',
            'created_at',
        ], '*');

        $history = DB::select('history', [
            'app_id',
            'user_id',
            'user_agent',
            'user_ip',
            'created_at',
        ], '*');

        return $this->respond('admin.twig', [
            'apps' => $apps,
            'users' => $users,
            'history' => $history,
            'admin' => Session::get('admin'),
        ]);
    }

    /**
     * Invite user.
     *
     * @param object $request
     * @param string $id
     *
     * @return Json
     */
    public function invite($request)
    {
        try {
            AdminValidator::invite($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        $code = Str::random();

        DB::create(
            'invites',
            [
                'code' => $code,
                'expires_at' => date('Y-m-d H:i:s', (strtotime(date('Y-m-d H:i:s')) + Config::get('jwt.invite'))),
            ]
        );

        try {
            $app_url = Config::get('app.url');
            Mail::send(
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
     * Update user.
     *
     * @param string $id
     *
     * @return Json
     */
    public function userToggle($id)
    {
        $user = DB::get('users', ['active'], ['id' => $id]);

        if (!$user) {
            return $this->respondJson(
                'User not found',
                [],
                400
            );
        }

        DB::update('users', ['active' => !$user['active']], ['id' => $id]);

        return $this->respondJson(
            'User Updated',
            ['reload' => true]
        );
    }

    /**
     * Delete history.
     *
     * @return Json
     */
    public function clearHistory()
    {
        DB::delete('history', ['user_ip[~]' => '%']);

        return $this->respondJson(
            'History Deleted',
            ['reload' => true]
        );
    }
}
