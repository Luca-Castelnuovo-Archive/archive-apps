<?php

namespace App\Controllers;

use Exception;
use CQ\DB\DB;
use CQ\Config\Config;
use CQ\Helpers\JWT;
use CQ\Helpers\Session;
use CQ\Helpers\Request;
use CQ\Controllers\Controller;
use App\Helpers\Gumroad;

class LaunchController extends Controller
{
    /**
     * Launch access to app
     *
     * @param string $id
     *
     * @return Html|Redirect
     */
    public function launch($id)
    {
        $app = DB::get('apps', ['url', 'active'], ['id' => $id]);

        if (!$app) {
            return $this->respond(
                'launch.twig',
                ['error' => 'App not found']
            );
        }

        if (!$app['active']) {
            return $this->respond(
                'launch.twig',
                ['error' => 'App not active']
            );
        }

        $license = DB::get('licenses', 'license', [
            'app_id' => $id,
            'user_id' => Session::get('id')
        ]);

        if (!$license) {
            return $this->respond(
                'launch.twig',
                ['error' => 'License not found']
            );
        }

        try {
            $gumroad = Gumroad::license($id, $license);
        } catch (Exception $e) {
            return $this->respond(
                'launch.twig',
                ['error' => 'License invalid']
            );
        }

        $jwt = JWT::create([
            'type' => 'auth',
            'sub' => Session::get('id'),
            'user_agent' => Request::userAgent(),
            'user_ip' => Request::ip(),
            'variant' => str_replace(['(', ')'], '', $gumroad->variants)
        ], Config::get('jwt.auth'), $app['url']);

        DB::create(
            'history',
            [
                'app_id' => $id,
                'user_id' => Session::get('id'),
                'user_agent' => Request::userAgent(),
                'user_ip' => Request::ip(),
            ]
        );

        return $this->redirect("{$app['url']}?code={$jwt}");
    }
}
