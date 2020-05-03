<?php

namespace App\Middleware;

use App\Helpers\AuthHelper;
use App\Helpers\SessionHelper;
use App\Helpers\JWTHelper;
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

            $msg = JWTHelper::create('message', ['message' => 'Session expired']);
            SessionHelper::set('return_to', $request->getUri());

            if ($request->isJSON) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Session expired',
                    'data' => ['redirect' => "/?msg={$msg}"]
                ], 403);
            }

            return new RedirectResponse("/?msg={$msg}");
        }

        return $next($request);
    }
}
