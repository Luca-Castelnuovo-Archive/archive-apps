<?php

namespace App\Controllers;

use DB;
use Exception;
use App\Helpers\SessionHelper;
use App\Helpers\GumroadHelper;
use App\Helpers\JWTHelper;
use Zend\Diactoros\ServerRequest;

class LaunchController extends Controller
{
    /**
     * Launch access to app
     *
     * @param ServerRequest $request
     * @param string $id
     * 
     * @return HtmlResponse|RedirectResponse
     */
    public function launch(ServerRequest $request, $id)
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
            'user_id' => SessionHelper::get('id')
        ]);

        if (!$license) {
            return $this->respond(
                'launch.twig',
                ['error' => 'License not found']
            );
        }

        try {
            $gumroad = GumroadHelper::license($id, $license);
        } catch (Exception $e) {
            return $this->respond(
                'launch.twig',
                ['error' => 'License invalid']
            );
        }

        $jwt = JWTHelper::create('auth', [
            'sub' => SessionHelper::get('id'),
            'variant' => str_replace(array('(', ')'), '', $gumroad->variants)
        ], $app['url']);

        DB::create(
            'history',
            [
                'app_id' => $id,
                'user_id' => SessionHelper::get('id'),
                'user_agent' => $request->getHeader('user-agent')[0],
                'user_ip' => $_SERVER['REMOTE_ADDR'],
            ]
        );

        return $this->redirect("{$app['url']}?code={$jwt}");
    }
}
