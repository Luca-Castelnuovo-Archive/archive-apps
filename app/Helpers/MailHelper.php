<?php

namespace App\Helpers;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class MailHelper
{
    /**
     * Send Emails
     *
     * @param array $config
     * 
     * @return 
     * @throws Exception 
     */
    public static function send($config)
    {
        $mail = new PHPMailer(true);

        try {
            // Server Conf
            $mail->isSMTP();
            $mail->Host       = config('smtp.host');
            $mail->SMTPAuth   = true;
            $mail->Username   = config('smtp.username');
            $mail->Password   = config('smtp.password');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->isHTML(true);

            // Template Conf
            $mail->addAddress($config['email_to']);
            $mail->Subject = $config['email_subject'];
            $mail->Body = $config['email_content'];

            $mail->setFrom(
                config('smtp.username'),
                $config['email_fromName'] ?: config('smtp.fromName')
            );

            if ($config['email_replyTo']) {
                $mail->addReplyTo($config['email_replyTo'], $config['email_replyTo']);
            }

            if ($config['email_cc']) {
                $mail->addCC($config['email_cc']);
            }

            if ($config['email_bcc']) {
                $mail->addBCC($config['email_bcc']);
            }

            $mail->send();
        } catch (MailException $e) {
            throw new Exception($mail->ErrorInfo);
        }
    }
}
