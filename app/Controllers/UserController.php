<?php

namespace App\Controllers;

use CQ\Controllers\Controller;
use CQ\DB\DB;
use CQ\Helpers\Session;

class UserController extends Controller
{
    /**
     * Dashboard screen
     *
     * @param object $request
     *
     * @return Html
     */
    public function dashboard($request)
    {
        $offer_code = $request->getQueryParams()['offer_code'] ?: 'free';

        $apps = DB::select(
            'apps',
            [
                'id',
                'active',
                'name',
                'url'
            ],
            [
                'ORDER' => ['name' => 'ASC']
            ]
        );

        $result = [];

        foreach ($apps as $app) {
            $license = DB::get('licenses', ['variant'], [
                'app_id' => $app['id'],
                'user_id' => Session::get('id')
            ]);

            $result[$app['id']] = $app;
            $result[$app['id']]['licensed'] = false;
            $result[$app['id']]['licensed_variant'] = '';

            if ($license) {
                $result[$app['id']]['licensed'] = true;
                $result[$app['id']]['licensed_variant'] = $license['variant'];
            }
        }

        $apps = array_values($result);

        return $this->respond('dashboard.twig', [
            'apps' => $apps,
            'offer_code' => $offer_code,
            'admin' => Session::get('admin')
        ]);
    }
}
