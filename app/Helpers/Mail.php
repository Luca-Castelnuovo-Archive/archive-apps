<?php

namespace App\Helpers;

use Exception;
use CQ\Config\Config;
use CQ\Helpers\Mail as MailHelper;

class Mail
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
        $data = [
            'email' => $to,
            'name' => $name,
            'btn_url' => $btn_url,
            'expires' => time() + Config::get("jwt.{$type}"),
            'subject' => Config::get("mail.{$type}.subject"),
            'preheader' => Config::get("mail.{$type}.preheader"),
            'message' => Config::get("mail.{$type}.message"),
            'btn_text' => Config::get("mail.{$type}.btn_text"),
        ];

        try {
            MailHelper::send(
                Config::get('mail.access_token'),
                $data,
                Config::get('app.url')
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
