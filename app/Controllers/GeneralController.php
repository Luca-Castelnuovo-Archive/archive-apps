<?php

namespace App\Controllers;

use Exception;
use App\Helpers\JWTHelper;
use App\Helpers\AuthHelper;
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

        try {
            $jwt = JWTHelper::valid('message', $msg);
            $msg = $jwt->message;
        } catch (Exception $e) {
            $msg = '';
        }

        return $this->respond('index.twig', [
            'message' => $msg,
            'logged_in' => AuthHelper::valid()
        ]);
    }

    /**
     * Error screen
     * 
     * @param string $httpcode
     *
     * @return HtmlResponse
     */
    public function error($httpcode)
    {
        switch ($httpcode) {
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
                $httpcode = 400;
                break;
        }

        return $this->respond('error.twig', [
            'code' => $httpcode,
            'short_message' => $short_message,
            'message' => $message
        ], $httpcode);
    }
}
