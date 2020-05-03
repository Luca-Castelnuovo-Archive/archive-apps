<?php

namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GumroadHelper
{
    /**
     * Product info based on permalink
     *
     * @param string $id
     * 
     * @return object
     * @throws Exception
     */
    public static function product($id)
    {
        $guzzle = new Client();

        try {
            $response = $guzzle->get("https://api.gumroad.com/v2/products/{$id}", [
                'headers' => [
                    'Origin' => config('app.url')
                ],
                'form_params' => [
                    'access_token' => config('gumroad.access_token')
                ]
            ]);
        } catch (GuzzleException $e) {
            throw new Exception('Unknown error');
        }

        $response = json_decode($response->getBody(true));

        if (!$response->success) {
            throw new Exception('Product not found');
        }

        return $response->product;
    }

    /**
     * Validates license
     *
     * @param string $id
     * @param string $license
     *
     * @return object
     * @throws Exception
     */
    public static function license($id, $license)
    {
        $guzzle = new Client();

        try {
            $response = $guzzle->post('https://api.gumroad.com/v2/licenses/verify', [
                'headers' => [
                    'Origin' => config('app.url')
                ],
                'form_params' => [
                    'product_permalink' => $id,
                    'license_key' => $license,
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new Exception('Invalid License');
        }

        $response = json_decode($response->getBody(true));

        if (!$response->success) {
            throw new Exception('Invalid License');
        }

        if ($response->purchase->refunded) {
            throw new Exception('Invalid License');
        }

        if ($response->purchase->subscription_cancelled_at) {
            throw new Exception('Invalid License');
        }

        return $response->purchase;
    }
}
