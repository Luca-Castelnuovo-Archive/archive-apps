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
     * @param string $captcha_secret
     *
     * @throws GuzzleException
     *
     * @return bool
     */
    public static function validate($captcha_response, $captcha_secret)
    {
        $guzzle_client = new Client();

        $response = $guzzle_client->request('POST', config('links.captcha'), [
            'form_params' => [
                'secret' => $captcha_secret,
                'response' => $captcha_response,
            ],
        ]);

        $response = json_decode($response->getBody());

        return $response->success;
    }
}
