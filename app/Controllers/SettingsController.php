<?php

namespace App\Controllers;

use DB;
use Exception;
use App\Helpers\SessionHelper;
use App\Validators\UserValidator;
use Zend\Diactoros\ServerRequest;

class SettingsController extends Controller
{
    /**
     * View settings
     *     * 
     * @return HtmlResponse
     */
    public function view()
    {
        $settings = DB::get('users', [
            'github',
            'google',
            'email'
        ], ['id' => SessionHelper::get('id')]);

        $licenses = DB::select('licenses', [
            'app_id',
            'license',
            'created_at'
        ], [
            'user_id' => SessionHelper::get('id')
        ]);

        $apps = DB::select(
            'apps',
            [
                'gumroad_id',
                'name'
            ],
            [
                "ORDER" => ["name" => "ASC"]
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
            'apps' => $apps
        ]);
    }

    /**
     * Add login option
     *
     * @param ServerRequest $request
     * 
     * @return JsonResponse
     */
    public function addLogin(ServerRequest $request)
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

        if (DB::get('users', $type, ['id' => SessionHelper::get('id')])) {
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

        DB::update('users', [$type => $data], ['id' => SessionHelper::get('id')]);

        return $this->respondJson(
            'Login Added',
            ['reload' => true]
        );
    }

    /**
     * Remove login option
     *
     * @param ServerRequest $request
     * 
     * @return JsonResponse
     */
    public function removeLogin(ServerRequest $request)
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
        ], ['id' => SessionHelper::get('id')]);

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

        DB::update('users', [$type => null], ['id' => SessionHelper::get('id')]);

        return $this->respondJson(
            'Login Removed',
            ['reload' => true]
        );
    }

    /**
     * Remove account
     * 
     * @return JsonResponse
     */
    public function removeAccount()
    {
        // TODO: activate function
        return $this->respondJson(
            'Access Denied',
            [],
            400
        );

        DB::delete('users', ['id' => SessionHelper::get('id')]);

        // TODO: reset session;

        return $this->respondJson(
            'Account Deleted',
            ['redirect' => '/auth/logout']
        );
    }
}
