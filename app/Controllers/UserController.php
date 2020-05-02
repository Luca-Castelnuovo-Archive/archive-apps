<?php

namespace App\Controllers;

use DB;
use App\Helpers\SessionHelper;

class UserController extends Controller
{
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
                'active',
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
}
