<?php

namespace App\Controllers;

use Exception;
use CQ\DB\DB;
use CQ\Helpers\Session;
use CQ\Controllers\Controller;
use App\Validators\UserValidator;

class SettingsController extends Controller
{
    /**
     * View settings
     *
     * @return Html
     */
    public function view()
    {
        $settings = DB::get('users', [
            'github',
            'google',
            'email'
        ], ['id' => Session::get('id')]);

        $licenses = DB::select('licenses', [
            'app_id',
            'license',
            'variant',
            'created_at'
        ], [
            'user_id' => Session::get('id')
        ]);

        $apps = DB::select(
            'apps',
            [
                'id',
                'name'
            ],
            [
                'ORDER' => ['name' => 'ASC']
            ]
        );

        $result = [];

        foreach ($licenses as $license) {
            $app = DB::get('apps', 'name', ['id' => $license['app_id']]);

            $result[$license['license']] = $license;
            $result[$license['license']]['name'] = $app;
        }

        $licenses = array_values($result);

        return $this->respond('settings.twig', [
            'settings' => $settings,
            'licenses' => $licenses,
            'apps' => $apps,
            'admin' => Session::get('admin')
        ]);
    }

    /**
     * Add login option
     *
     * @param object $request
     *
     * @return Json
     */
    public function addLogin($request)
    {
        try {
            UserValidator::addLogin($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        $type = $request->data->type;
        $data = $request->data->id;

        if (DB::get('users', $type, ['id' => Session::get('id')])) {
            return $this->respondJson(
                "Please unlink {$type} before relinking it",
                [],
                400
            );
        }

        if (DB::has('users', [$type => $data])) {
            return $this->respondJson(
                "This {$type} account is already used, please use another",
                [],
                400
            );
        }

        DB::update('users', [$type => $data], ['id' => Session::get('id')]);

        return $this->respondJson(
            'Login Added',
            ['reload' => true]
        );
    }

    /**
     * Remove login option
     *
     * @param object $request
     *
     * @return Json
     */
    public function removeLogin($request)
    {
        try {
            UserValidator::removeLogin($request->data);
        } catch (Exception $e) {
            return $this->respondJson(
                'Provided data was malformed',
                json_decode($e->getMessage()),
                422
            );
        }

        $type = $request->data->type;
        $settings = DB::get('users', [
            'github',
            'google',
            'email'
        ], ['id' => Session::get('id')]);

        switch ($type) {
            case 'github':
                if (!$settings['google'] && !$settings['email']) {
                    return $this->respondJson(
                        'You have to have at least one login option',
                        [],
                        400
                    );
                }
                break;

            case 'google':
                if (!$settings['github'] && !$settings['email']) {
                    return $this->respondJson(
                        'You have to have at least one login option',
                        [],
                        400
                    );
                }
                break;

            case 'email':
                if (!$settings['google'] && !$settings['github']) {
                    return $this->respondJson(
                        'You have to have at least one login option',
                        [],
                        400
                    );
                }
                break;
        }

        DB::update('users', [$type => null], ['id' => Session::get('id')]);

        return $this->respondJson(
            'Login Removed',
            ['reload' => true]
        );
    }

    /**
     * Remove account
     *
     * @return Json
     */
    public function removeAccount()
    {
        DB::delete('users', ['id' => Session::get('id')]);

        Session::destroy();

        return $this->respondJson(
            'Account Deleted',
            ['redirect' => '/auth/logout']
        );
    }
}
