<?php

namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CaptchaHelper
{
    /**
     * Validates the captcha response.
     *
     * @param string $captcha_response
     *
     * @throws Exception
     */
    public static function validate($captcha_response)
    {
        $guzzle = new Client();

        try {
            $guzzle->post(config('captcha.endpoint'), [
                'headers' => [
                    'Origin' => config('app.url')
                ],
                'form_params' => [
                    'secret' => config('captcha.secret_key'),
                    'response' => $captcha_response,
                ],
            ]);
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody(true));

            throw new Exception(json_encode($response->{'error-codes'}));
        }
    }
}
