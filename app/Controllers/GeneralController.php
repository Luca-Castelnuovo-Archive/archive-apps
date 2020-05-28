<?php

namespace App\Controllers;

use CQ\Helpers\Auth;
use CQ\Config\Config;
use CQ\Controllers\Controller;

class GeneralController extends Controller
{
    /**
     * Index screen
     * 
     * @param object $request
     * 
     * @return Html
     */
    public function index($request)
    {
        $msg = $request->getQueryParams()['msg'] ?: '';

        if ($msg) {
            switch ($msg) {
                case 'logout':
                    $msg = 'You have been logged out!';
                    break;

                case 'notfound':
                    $msg = 'Account not found!';
                    break;

                case 'deactivated':
                    $msg = 'Your account has been deactivated! Contact the administrator';
                    break;

                case 'state':
                    $msg = 'State is invalid!';
                    break;

                case 'stateMail':
                    $msg = 'Please open link on the same device that requested the login!';
                    break;

                case 'expired':
                    $msg = 'Session expired!';
                    break;

                case 'register':
                    $msg = 'You can now login!';
                    break;

                case 'token':
                    $msg = 'Invalid authentication!';
                    break;

                default:
                    $msg = '';
                    break;
            }
        }

        return $this->respond('index.twig', [
            'message' => $msg,
            'logged_in' => Auth::valid(),
            'captcha_site_key' => Config::get('captcha.site_key')
        ]);
    }

    /**
     * Show JWT public_key
     * 
     * @return Json
     */
    public function jwt()
    {
        return $this->respondJson('jwt public info', [
            'algorithm' => Config::get('jwt.algorithm'),
            'iss' =>  Config::get('jwt.iss'),
            'public_key' => Config::get('jwt.public_key')
        ]);
    }

    /**
     * Error screen
     * 
     * @param string $httpcode
     * 
     * @return Html
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
            case '500':
                $short_message = 'Oops! Server error';
                $message = 'We are experiencing some technical issues';
                break;

            default:
                $short_message = 'Oops! Unknown Error';
                $message = 'Unknown error occured';
                $code = 400;
                break;
        }

        return $this->respond('error.twig', [
            'code' => $code,
            'short_message' => $short_message,
            'message' => $message
        ], $code);
    }
}
