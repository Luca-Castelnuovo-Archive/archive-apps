<?php

namespace App\Middleware;

use CQ\Helpers\Request;
use CQ\Helpers\Session as SessionHelper;
use CQ\Middleware\Middleware;
use CQ\Response\Json;
use CQ\Response\Redirect;
use App\Helpers\Auth;

class Session extends Middleware
{
    /**
     * Validate PHP session.
     *
     * @param $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (!Auth::valid()) {
            SessionHelper::destroy();

            SessionHelper::set('return_to', $request->getUri());

            if (Request::isJson($request)) {
                return new Json([
                    'success' => false,
                    'message' => 'Session expired',
                    'data' => ['redirect' => '/?msg=expired']
                ], 403);
            }

            return new Redirect('/?msg=expired', 403);
        }

        return $next($request);
    }
}
