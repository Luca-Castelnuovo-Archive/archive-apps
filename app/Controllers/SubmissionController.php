<?php

namespace App\Controllers;

use DB;
use Exception;
use App\Helpers\MailHelper;
use App\Helpers\SessionHelper;
use App\Helpers\CaptchaHelper;
use Zend\Diactoros\ServerRequest;

class SubmissionController extends Controller
{
    /**
     * Handle submission in JSON format.
     *
     * @param ServerRequest $request
     * 
     * @return JsonResponse
     */
    public function submit(ServerRequest $request)
    {
        $template = DB::get('templates', [
            'id',
            'captcha_key',
            'email_to',
            'email_replyTo',
            'email_cc',
            'email_bcc',
            'email_fromName',
            'email_subject',
            'email_content'
        ], ['uuid' => $request->uuid]);

        try {
            if ($template['captcha_key']) {
                if (!CaptchaHelper::validate(
                    $request->data->{"g-recaptcha-response"},
                    $template['captcha_key']
                )) {
                    throw new Exception('Invalid captcha response');
                }
            }

            MailHelper::send([
                'email_to' => $this->renderFromText($template['email_to'], (array) $request->data),
                'email_replyTo' => $this->renderFromText($template['email_replyTo'], (array) $request->data),
                'email_cc' => $this->renderFromText($template['email_cc'], (array) $request->data),
                'email_bcc' => $this->renderFromText($template['email_bcc'], (array) $request->data),
                'email_fromName' => $this->renderFromText($template['email_fromName'], (array) $request->data),
                'email_subject' => $this->renderFromText($template['email_subject'], (array) $request->data),
                'email_content' => $this->renderFromText($template['email_content'], (array) $request->data)
            ]);
        } catch (Exception $e) {
            return $this->respondJsonError('Mail Error', $e->getMessage(), 400);
        }

        DB::create(
            'history',
            [
                'template_id' => $template['id'],
                'template_params' => json_encode($request->data),
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'origin' => $request->origin ?: 'unknown',
            ]
        );

        return $this->respondJson($request->data);
    }
}
