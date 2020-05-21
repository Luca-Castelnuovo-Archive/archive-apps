<?php

namespace App\Middleware;

use CQ\Helpers\Request;
use CQ\Helpers\Session;
use CQ\Middleware\Middleware;
use CQ\Response\Json;
use CQ\Response\Redirect;

class Admin implements Middleware
{
    /**
     * Validate admin access
     *
     * @param $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (!Session::get('admin')) {
            if (Request::isJson($request)) {
                return new Json([
                    'success' => false,
                    'message' => 'Access Denied',
                    'data' => []
                ], 403);
            }

            return new Redirect('/dashboard', 403);
        }

        return $next($request);
    }
}
