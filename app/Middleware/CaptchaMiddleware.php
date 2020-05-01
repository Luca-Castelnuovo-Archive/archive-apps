<?php

namespace App\Middleware;

use Exception;
use App\Validators\CaptchaValidator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use MiladRahimi\PhpRouter\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class CaptchaMiddleware implements Middleware
{
    /**
     * Validate captcha response
     *
     * @param Request $request
     * @param $next
     *
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, $next)
    {
        $guzzle = new Client();

        try {
            CaptchaValidator::submit($request->data);
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Provided data was malformed',
                'data' => $e->getMessage()
            ], 422);
        }

        try {
            $guzzle->post(config('captcha.endpoint'), [
                'headers' => [
                    'Origin' => config('app.url')
                ],
                'form_params' => [
                    'secret' => config('captcha.secret_key'),
                    'response' => $request->data->{config('captcha.frontend_class') . '-response'}
                ],
            ]);
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody(true));

            return new JsonResponse([
                'success' => false,
                'message' => 'Please complete captcha',
                'data' => $response->{'error-codes'}
            ], 422);
        }

        return $next($request);
    }
}
