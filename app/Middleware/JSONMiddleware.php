<?php

namespace App\Middleware;

use App\Helpers\StringHelper;
use MiladRahimi\PhpRouter\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class JSONMiddleware implements Middleware
{
    /**
     * If requests contains JSON interpret it
     * Also validate that the provided JSON is valid.
     *
     * @param Request $request
     * @param $next
     *
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, $next)
    {
        if (!StringHelper::contains($request->getHeader('content-type')[0], '/json')) {
            return new JsonResponse([
                'success' => false,
                'message' => "Content-Type should be 'application/json'",
                'data' => []
            ], 415);
        }

        $data = json_decode($request->getBody()->getContents());

        if ((JSON_ERROR_NONE !== json_last_error())) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Problems parsing provided JSON',
                'data' => []
            ], 415);
        }

        $request->data = $data;
        $request->isJSON = true;

        return $next($request);
    }
}
