<?php

namespace App\Controllers;

use DB;
use Exception;
use App\Helpers\SessionHelper;
use App\Helpers\JWTHelper;
use App\Validators\TemplateValidator;
use Ramsey\Uuid\Uuid;
use Zend\Diactoros\ServerRequest;

class TemplateController extends Controller
{
    /**
     * Create template
     *
     * @param ServerRequest $request
     *
     * @return JsonResponse
     */
    public function create(ServerRequest $request)
    {
        try {
            TemplateValidator::create($request->data);
        } catch (Exception $e) {
            return $this->respondJsonError(
                'invalid_input',
                json_decode($e->getMessage()),
                422
            );
        }

        DB::create(
            'templates',
            [
                'user_id' => SessionHelper::get('user_id'),
                'name' => $request->data->name,
                'uuid' => Uuid::uuid4()->toString(),
                'captcha_key' => $request->data->captcha_key,
                'email_to' => $request->data->email_to,
                'email_replyTo' => $request->data->email_replyTo,
                'email_cc' => $request->data->email_cc,
                'email_bcc' => $request->data->email_bcc,
                'email_fromName' => $request->data->email_fromName,
                'email_subject' => $request->data->email_subject,
                'email_content' => $request->data->email_content
            ]
        );

        return $this->respondJson();
    }

    /**
     * Update template
     *
     * @param ServerRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(ServerRequest $request, $id)
    {
        if (!$this->hasUserTemplate($id, SessionHelper::get('user_id'))) {
            return $this->respondJsonError(
                'template_not_owned',
                'The user doesn\'t own the template',
                403
            );
        }

        try {
            TemplateValidator::update($request->data);
        } catch (Exception $e) {
            return $this->respondJsonError(
                'invalid_input',
                json_decode($e->getMessage()),
                422
            );
        }

        $template = DB::get(
            'templates',
            [
                'name',
                'captcha_key',
                'email_to',
                'email_replyTo',
                'email_cc',
                'email_bcc',
                'email_fromName',
                'email_subject',
                'email_content'
            ],
            [
                'id' => $id
            ]
        );

        DB::update(
            'templates',
            [
                'name' => $request->data->name ?: $template['name'],
                'captcha_key' => $request->data->captcha_key ?: $template['captcha_key'],
                'email_to' => $request->data->email_to ?: $template['email_to'],
                'email_replyTo' => $request->data->email_replyTo ?: $template['email_replyTo'],
                'email_cc' => $request->data->email_cc ?: $template['email_cc'],
                'email_bcc' => $request->data->email_bcc ?: $template['email_bcc'],
                'email_fromName' => $request->data->email_fromName ?: $template['email_fromName'],
                'email_subject' => $request->data->email_subject ?: $template['email_subject'],
                'email_content' => $request->data->email_content ?: $template['email_content'],
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'id' => $id
            ]
        );

        return $this->respondJson();
    }

    /**
     * Delete template
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        if (!$this->hasUserTemplate($id, SessionHelper::get('user_id'))) {
            return $this->respondJsonError(
                'template_not_owned',
                'The user doesn\'t own the template',
                403
            );
        }

        DB::delete('templates', [
            'id' => $id,
        ]);

        DB::delete('history', [
            'template_id' => $id,
        ]);

        return $this->respondJson();
    }

    /**
     * Create access_key
     *
     * @param ServerRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function createKey(ServerRequest $request, $id)
    {
        if (!$this->hasUserTemplate($id, SessionHelper::get('user_id'))) {
            return $this->respondJsonError(
                'template_not_owned',
                'The user doesn\'t own the template',
                403
            );
        }

        try {
            TemplateValidator::createKey($request->data);
        } catch (Exception $e) {
            return $this->respondJsonError(
                'invalid_input',
                json_decode($e->getMessage()),
                422
            );
        }

        $uuid = DB::get('templates', 'uuid', [
            'id' => $id
        ]);

        $key = JWTHelper::create('submission', [
            'sub' => $uuid,
            'allowed_origin' => $request->data->allowed_origin
        ]);

        return $this->respondJson([
            'key' => $key
        ]);
    }

    /**
     * Reset all access_key
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function resetKey($id)
    {
        if (!$this->hasUserTemplate($id, SessionHelper::get('user_id'))) {
            return $this->respondJsonError(
                'template_not_owned',
                'The user doesn\'t own the template',
                403
            );
        }

        DB::update('templates', [
            'uuid' => Uuid::uuid4()->toString(),
            'updated_at' => date("Y-m-d H:i:s")
        ], $id);

        return $this->respondJson();
    }
}
