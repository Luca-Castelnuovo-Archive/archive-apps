<?php

namespace App\Middleware;

use Exception;
use CQ\Response\Json;
use CQ\Config\Config;
use CQ\Middleware\Middleware;
use App\Validators\CaptchaValidator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Captcha implements Middleware
{
    /**
     * Validate captcha response
     *
     * @param $request
     * @param $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        $guzzle = new Client();

        try {
            CaptchaValidator::submit($request->data);
        } catch (Exception $e) {
            return new Json([
                'success' => false,
                'message' => 'Provided data was malformed',
                'data' => $e->getMessage()
            ], 422);
        }

        try {
            $guzzle->post('https://hcaptcha.com/siteverify', [
                'headers' => [
                    'Origin' => Config::get('app.url')
                ],
                'form_params' => [
                    'secret' => Config::get('captcha.secret_key'),
                    'response' => $request->data->{'h-captcha-response'}
                ],
            ]);
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody(true));

            return new Json([
                'success' => false,
                'message' => 'Please complete captcha',
                'data' => $response->{'error-codes'}
            ], 422);
        }

        return $next($request);
    }
}
