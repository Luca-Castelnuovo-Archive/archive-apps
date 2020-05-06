<?php

namespace App\Middleware;

use App\Helpers\JWTHelper;
use App\Helpers\AuthHelper;
use App\Helpers\StringHelper;
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

            $msg = JWTHelper::create([
                'type' => 'message',
                'message' => 'Session expired'
            ], config('jwt.message'));
            SessionHelper::set('return_to', $request->getUri());

            if (StringHelper::contains($request->getHeader('content-type')[0], '/json')) {
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
