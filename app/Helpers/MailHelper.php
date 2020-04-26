<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class MailHelper
{
    /**
     * Send Emails
     *
     * @param array $config
     * 
     * @return 
     */
    public static function send($type, $to, $name, $btn_url)
    {
        $guzzle = new Client();

        $config = config('mail')[$type];
        $access_token = config('mail.access_token');

        $response = $guzzle->request('POST', config('mail.endpoint'), [
            'headers' => [
                'Authorization' => "Bearer {$access_token}",
            ],
            'json' => [
                'email' => $to,
                'name' => $name,
                'btn_url' => $btn_url,
                'subject' => $config->subject,
                'preheader' => $config->preheader,
                'message' => $config->message,
                'btn_text' => $config->btn_text,
            ],
        ]);

        $response = json_decode($response->getBody());

        return $response->success;
    }
}
