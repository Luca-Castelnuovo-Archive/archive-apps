<?php

namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class MailHelper
{
    /**
     * Send Emails
     *
     * @param string $type
     * @param string $to
     * @param string $name
     * @param string $btn_url
     * 
     * @throws Exception
     */
    public static function send($type, $to, $name, $btn_url)
    {
        $guzzle = new Client();

        $config = config("mail.{$type}");
        $access_token = config('mail.access_token');

        try {
            $guzzle->post(config('mail.endpoint'), [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}",
                    'Origin' => config('app.url')
                ],
                'json' => [
                    'email' => $to,
                    'name' => $name,
                    'btn_url' => $btn_url,
                    'subject' => $config['subject'],
                    'preheader' => $config['preheader'],
                    'message' => $config['message'],
                    'btn_text' => $config['btn_text'],
                ],
            ]);
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody(true));

            throw new Exception(json_encode($response->errors));
        }
    }
}
