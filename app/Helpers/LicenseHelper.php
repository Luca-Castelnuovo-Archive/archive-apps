<?php

namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class LicenseHelper
{
    /**
     * Validates the license key
     *
     * @param string $gumroad_id
     * @param string $license
     *
     * @return bool
     */
    public static function validate($gumroad_id, $license)
    {
        $guzzle = new Client();

        try {
            $response = $guzzle->post('https://api.gumroad.com/v2/licenses/verify', [
                'headers' => [
                    'Origin' => config('app.url')
                ],
                'form_params' => [
                    'product_permalink' => $gumroad_id,
                    'license_key' => $license,
                ],
            ]);
        } catch (GuzzleException $e) {
            return false;
        }

        $response = json_decode($response->getBody(true));

        return $response->success;
    }
}
