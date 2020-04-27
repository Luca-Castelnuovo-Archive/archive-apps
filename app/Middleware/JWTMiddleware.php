<?php

namespace App\Middleware;

use DB;
use Exception;
use App\Helpers\JWTHelper;
use MiladRahimi\PhpRouter\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class JWTMiddleware implements Middleware
{
    /**
     * Validate JWT token.
     *
     * @param Request $request
     * @param $next
     *
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, $next)
    {
        $authorization_header = $request->getHeader('authorization')[0];
        $access_token = str_replace('Bearer ', '', $authorization_header);

        try {
            $credentials = JWTHelper::valid('submission', $access_token);
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'status' => 401,
                    'title' => 'JWT Error',
                    'detail' => $e->getMessage()
                ]
            ], 401);
        }

        if (!DB::has('templates', ['uuid' =>  $credentials->sub])) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'status' => 404,
                    'title' => 'template_not_found',
                    'detail' => 'this uuid is not connected to a template'
                ]
            ], 404);
        }

        $request->uuid = $credentials->sub;

        return $next($request);
    }
}
