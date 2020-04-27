<?php

use App\Middleware\CORSMiddleware;
use App\Middleware\JSONMiddleware;
use App\Middleware\JWTMiddleware;
use App\Middleware\SessionMiddleware;
use App\Middleware\RateLimitMiddleware;
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

    $router->post('/email/request', 'EmailAuthController@request', JSONMiddleware::class);
    $router->get('/email/callback', 'EmailAuthController@callback');

    $router->get('/google/request', 'GoogleAuthController@request');
    $router->get('/google/callback', 'GoogleAuthController@callback');

    $router->get('/github/request', 'GithubAuthController@request');
    $router->get('/github/callback', 'GithubAuthController@callback');

    $router->get('/invite', 'InviteAuthController@view');
    $router->post('/invite', 'InviteAuthController@invite', JSONMiddleware::class);
});

$router->group(['prefix' => '/user', 'middleware' => SessionMiddleware::class], function (Router $router) {
    $router->get('/dashboard', 'UserController@dashboard');
    $router->get('/settings', 'UserController@settingsView');
    $router->put('/settings', 'UserController@settings');
});

$router->group(['prefix' => '/app', 'middleware' => SessionMiddleware::class], function (Router $router) {
    $router->post('/', 'AppController@create');
    $router->put('/{id}', 'AppController@update');
    $router->put('/{id}/toggle', 'AppController@toggleActive');
    $router->delete('/{id}', 'AppController@delete');
});



$router->group(['middleware' => [CORSMiddleware::class, RateLimitMiddleware::class, JWTMiddleware::class, JSONMiddleware::class]], function (Router $router) {
    $router->any('/submit', 'SubmissionController@submit');
});


try {
    $router->dispatch();
} catch (RouteNotFoundException $e) {
    $router->getPublisher()->publish(new RedirectResponse('/error/404', 404));
} catch (Throwable $e) {
    $router->getPublisher()->publish(new RedirectResponse("/error/500?e={$e}", 500));
}
