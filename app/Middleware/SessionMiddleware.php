<?php

namespace App\Middleware;

use App\Helpers\AuthHelper;
use App\Helpers\SessionHelper;
use MiladRahimi\PhpRouter\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Response\JsonResponse;

class SessionMiddleware implements Middleware
{
    /**
     * Validate PHP session.
     *
     * @param Request $request
     * @param $next
     *
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, $next)
    {
        if (!AuthHelper::valid()) {
            SessionHelper::destroy();

            if ($request->isJSON) {
                // TODO: reset session;

                return new JsonResponse([
                    'success' => false,
                    'message' => 'Session expired or IP mismatch',
                    'data' => ['redirect' => '/auth/logout']
                ], 403);
            }

            return new RedirectResponse('/auth/logout', 403);
        }

        return $next($request);
    }
}
