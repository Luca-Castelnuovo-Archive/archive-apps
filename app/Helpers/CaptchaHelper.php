<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CaptchaHelper
{
    /**
     * Validates the captcha response.
     *
     * @param string $captcha_response
     *
     * @throws GuzzleException
     *
     * @return bool
     */
    public static function validate($captcha_response)
    {
        $guzzle_client = new Client();

        $response = $guzzle_client->request('POST', config('captcha.endpoint'), [
            'form_params' => [
                'secret' => config('captcha.secret_key'),
                'response' => $captcha_response,
            ],
        ]);

        $response = json_decode($response->getBody());

        return $response->success;
    }
}
