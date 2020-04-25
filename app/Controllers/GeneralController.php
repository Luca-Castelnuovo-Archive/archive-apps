<?php

namespace App\Controllers;

use DB;
use Exception;
use App\Helpers\JWTHelper;
use App\Helpers\SessionHelper;
use Zend\Diactoros\ServerRequest;

class GeneralController extends Controller
{
    /**
     * Login screen
     * 
     * @return HtmlResponse
     */
    public function index(ServerRequest $request)
    {
        $msg = $request->getQueryParams()['msg'] ?: '';

        if ($msg) {
            try {
                $claims = JWTHelper::valid('message', $msg);
                $msg = $claims->message;
            } catch (Exception $e) {
                $msg = '';
            }
        }

        return $this->respond('index.twig', [
            'message' => $msg,
            'logged_in' => SessionHelper::valid()
        ]);
    }

    /**
     * Docs screen
     * 
     * @return RedirectResponse
     */
    public function docs()
    {
        return $this->redirect(config('links.docs'));
    }

    /**
     * SDK screen
     * 
     * @return RedirectResponse
     */
    public function sdk()
    {
        return $this->redirect(config('links.sdk'));
    }

    /**
     * Template screen
     * 
     * @return RedirectResponse
     */
    public function template()
    {
        return $this->redirect(config('links.template'));
    }

    /**
     * Error screen
     * 
     * @param string $code
     *
     * @return HtmlResponse
     */
    public function error($code)
    {
        switch ($code) {
            case '403':
                $short_message = 'Oops! Access denied';
                $message = 'Access to this page is forbidden';
                break;
            case '404':
                $short_message = 'Oops! Page not found';
                $message = 'We are sorry, but the page you requested was not found';
                break;
            case '422':
                $short_message = 'Oops! Parameters missing';
                $message = 'The page you requested missed required parameters';
                break;
            case '500':
                $short_message = 'Oops! Server error';
                $message = 'We are experiencing some technical issues';
                break;
            case '502':
                $short_message = 'Oops! Proxy error';
                $message = 'We are experiencing some technical issues';
                break;

            default:
                $short_message = 'Oops! Unknown Error';
                $message = 'Unknown error occured';
                $code = 500;
                break;
        }

        return $this->respond('error.twig', [
            'code' => $code,
            'short_message' => $short_message,
            'message' => $message
        ], $code);
    }

    /**
     * Dashboard screen
     *
     * @return HtmlResponse
     */
    public function dashboard()
    {
        $templates = DB::select(
            'templates',
            [
                'id',
                'name',
                'captcha_key',
                'email_to',
                'email_replyTo',
                'email_cc',
                'email_bcc',
                'email_fromName',
                'email_subject',
                'email_content',
                'updated_at',
                'created_at'
            ],
            [
                'user_id' => SessionHelper::get('user_id'),
                "ORDER" => ["id" => "ASC"]
            ]
        );

        $result = [];

        foreach ($templates as $template) {
            $history = DB::select(
                'history',
                [
                    'template_params[JSON]',
                    'user_ip',
                    'origin',
                    'created_at'
                ],
                [
                    'template_id' => $template['id'],
                    "ORDER" => ["id" => "ASC"]
                ]
            );

            $result[$template['id']] = $template;
            $result[$template['id']]['history'] = $history;
        }

        $templates = array_values($result);

        return $this->respond('dashboard.twig', [
            'templates' => $templates
        ]);
    }
}
