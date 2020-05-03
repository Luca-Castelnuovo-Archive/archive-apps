<?php

namespace App\Controllers;

use DB;
use App\Helpers\SessionHelper;
use Zend\Diactoros\ServerRequest;

class UserController extends Controller
{
    /**
     * Dashboard screen
     *
     * @param ServerRequest $request
     * 
     * @return HtmlResponse
     */
    public function dashboard(ServerRequest $request)
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
                "ORDER" => ["name" => "ASC"]
            ]
        );

        $result = [];

        foreach ($apps as $app) {
            $license = DB::get('licenses', ['variant'], [
                'app_id' => $app['id'],
                'user_id' => SessionHelper::get('id')
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
            'offer_code' => $offer_code
        ]);
    }
}
