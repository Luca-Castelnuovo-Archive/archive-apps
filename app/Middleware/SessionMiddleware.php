<?php

namespace App\Middleware;

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
        if (!SessionHelper::valid()) {
            SessionHelper::destroy();

            if (!$request->isJSON) {
                return new RedirectResponse('/auth/logout', 403);
            }

            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'status' => 403,
                    'title' => 'invalid_session',
                    'detail' => 'Session expired or IP mismatch'
                ]
            ], 403);
        }

        SessionHelper::set('last_activity', time());

        return $next($request);
    }
}
