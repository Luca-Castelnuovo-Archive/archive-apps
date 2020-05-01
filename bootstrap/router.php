<?php

use App\Middleware\CaptchaMiddleware;
use App\Middleware\JSONMiddleware;
use App\Middleware\SessionMiddleware;
use MiladRahimi\PhpRouter\Router;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;
use Zend\Diactoros\Response\RedirectResponse;

$router = new Router('', 'App\Controllers');
$router->define('httpcode', '[0-9]+');
$router->define('path', '[a-zA-Z]+');

$router->get('/', 'GeneralController@index');
$router->get('/error/{httpcode}', 'GeneralController@error');

$router->group(['prefix' => '/auth', 'namespace' => 'App\Controllers\Auth'], function (Router $router) {
    $router->get('/logout', 'AuthController@logout');

    $router->post('/invite', 'RegisterAuthController@invite', [JSONMiddleware::class, CaptchaMiddleware::class]);
    $router->post('/license', 'RegisterAuthController@license', [JSONMiddleware::class, CaptchaMiddleware::class]);
    $router->get('/register', 'RegisterAuthController@registerView');
    $router->post('/register', 'RegisterAuthController@register', JSONMiddleware::class);

    $router->post('/email/request', 'EmailAuthController@request', [JSONMiddleware::class, CaptchaMiddleware::class]);
    $router->get('/email/callback', 'EmailAuthController@callback');
    $router->get('/google/request', 'GoogleAuthController@request');
    $router->get('/google/callback', 'GoogleAuthController@callback');
    $router->get('/github/request', 'GithubAuthController@request');
    $router->get('/github/callback', 'GithubAuthController@callback');
});

$router->group(['prefix' => '/user', 'middleware' => SessionMiddleware::class], function (Router $router) {
    $router->get('/dashboard', 'UserController@dashboard');
    $router->get('/settings', 'UserController@settingsView');

    $router->post('/login', 'UserController@addLogin', JSONMiddleware::class);
    $router->delete('/login', 'UserController@removeLogin', JSONMiddleware::class);
    $router->delete('/account', 'UserController@removeAccount');
});

$router->group(['prefix' => '/license', 'middleware' => SessionMiddleware::class], function (Router $router) {
    $router->post('', 'LicenseController@create', JSONMiddleware::class);
    $router->delete('', 'LicenseController@remove', JSONMiddleware::class);
});

$router->group(['prefix' => '/app', 'middleware' => SessionMiddleware::class], function (Router $router) {
    $router->post('/', 'AppController@create');
    $router->put('/{id}', 'AppController@update');
    $router->put('/{id}/toggle', 'AppController@toggleActive');
    $router->delete('/{id}', 'AppController@delete');
});

try {
    $router->dispatch();
} catch (RouteNotFoundException $e) {
    $router->getPublisher()->publish(new RedirectResponse('/error/404', 404));
} catch (Throwable $e) {
    $router->getPublisher()->publish(new RedirectResponse("/error/500?e={$e}", 500));
}
