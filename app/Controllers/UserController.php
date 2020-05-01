<?php

namespace App\Controllers;

use DB;
use App\Helpers\SessionHelper;
use Zend\Diactoros\ServerRequest;

class UserController extends Controller
{
    /**
     * Update User
     * 
     * Link Github Account
     * Link Google Account
     *
     */

    /**
     * Dashboard screen
     *
     * @return HtmlResponse
     */
    public function dashboard()
    {
        $apps = DB::select(
            'apps',
            [
                'id',
                'gumroad_id',
                'name',
                'url'
            ],
            [
                "ORDER" => ["name" => "ASC"]
            ]
        );

        $result = [];

        foreach ($apps as $app) {
            $license = DB::get('licenses', 'license', [
                'app_id' => $app['id'],
                'user_id' => SessionHelper::get('id')
            ]);

            $result[$app['id']] = $app;
            $result[$app['id']]['licensed'] = (bool) $license;
        }

        $apps = array_values($result);

        return $this->respond('user/dashboard.twig', [
            'apps' => $apps
        ]);
    }

    /**
     * View settings
     *
     * @param ServerRequest $request
     * 
     * @return JsonResponse
     */
    public function settingsView(ServerRequest $request)
    {
        // show settings
        $settings = [];
        return $this->respond('user/settings.twig', [
            'settings' => $settings
        ]);
    }

    /**
     * Update settings
     *
     * @param ServerRequest $request
     * 
     * @return JsonResponse
     */
    public function settings(ServerRequest $request)
    {
        // update settings

        return $this->respondJson($request->data);
    }
}
