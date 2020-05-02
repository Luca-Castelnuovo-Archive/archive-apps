<?php

namespace App\Controllers;

use App\Helpers\SessionHelper;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class Controller
{
    private $twig;

    /**
     * Provide access for child classes
     * 
     * @return void
     */
    public function __construct()
    {
        $loader = new FilesystemLoader('../views');
        $this->twig = new Environment($loader /* , ['cache' => '../storage/views'] */);
        $this->twig->addGlobal('analytics', config('analytics'));
        $this->twig->addGlobal('captcha', config('captcha'));
    }

    /**
     * Shorthand redirect function
     *
     * @param string $to
     * @param integer $code optional
     * 
     * @return RedirectResponse
     */
    protected function redirect($to, $code = 302)
    {
        return new RedirectResponse($to, $code);
    }

    /**
     * Shorthand HTML response function
     *
     * @param string $view
     * @param array $parameters
     * @param integer $code optional
     * 
     * @return HtmlResponse
     */
    protected function respond($view, $parameters = [], $code = 200)
    {
        return new HtmlResponse(
            $this->twig->render(
                $view,
                $parameters
            ),
            $code
        );
    }

    /**
     * Shorthand JSON response function
     * 
     * @param string $message
     * @param array $data optional
     * @param integer $code optional
     * 
     * @return JsonResponse
     */
    protected function respondJson($message, $data = [], $code = 200)
    {
        return new JsonResponse([
            'success' => $code === 200,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Check if user is admin
     *
     * @return boolean
     */
    protected function isUserAdmin()
    {
        return SessionHelper::get('admin');
    }
}
