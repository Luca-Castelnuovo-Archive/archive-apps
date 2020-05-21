<?php

use App\Middleware\Admin;
use CQ\Routing\Route;
use CQ\Routing\Middleware;
use CQ\Middleware\JSON;
use App\Middleware\Session;
use App\Middleware\Captcha;

Route::$router = $router->get();
Middleware::$router = $router->get();

Route::get('/', 'GeneralController@index');
Route::get('/jwt', 'GeneralController@jwt');
Route::get('/error/{code}', 'GeneralController@error');

Middleware::create(['prefix' => '/auth', 'namespace' => 'App\Controllers\Auth'], function () {
    Route::get('/logout', 'AuthController@logout');

    Route::post('/invite', 'RegisterAuthController@invite', [JSON::class, Captcha::class]);
    Route::get('/register', 'RegisterAuthController@registerView');
    Route::post('/register', 'RegisterAuthController@register', JSON::class);

    Route::post('/email/request', 'EmailAuthController@request', [JSON::class, Captcha::class]);
    Route::get('/email/callback', 'EmailAuthController@callback');
    Route::get('/google/request', 'GoogleAuthController@request');
    Route::get('/google/callback', 'GoogleAuthController@callback');
    Route::get('/github/request', 'GithubAuthController@request');
    Route::get('/github/callback', 'GithubAuthController@callback');
});

Middleware::create(['middleware' => [Session::class]], function () {
    Route::get('/dashboard', 'UserController@dashboard');
});

Middleware::create(['prefix' => '/settings', 'middleware' => [Session::class]], function () {
    Route::get('', 'SettingsController@view');
    Route::post('/login', 'SettingsController@addLogin', JSON::class);
    Route::delete('/login', 'SettingsController@removeLogin', JSON::class);
    Route::delete('/account', 'SettingsController@removeAccount');
});

Middleware::create(['prefix' => '/license', 'middleware' => [Session::class]], function () {
    Route::get('/{id}/{offer_code}', 'LicenseController@popup');
    Route::post('', 'LicenseController@create', JSON::class);
    Route::delete('', 'LicenseController@remove', JSON::class);
});

Middleware::create(['prefix' => '/launch', 'middleware' => [Session::class]], function () {
    Route::get('/{id}', 'LaunchController@launch');
});


Middleware::create(['prefix' => '/app', 'middleware' => [Session::class, Admin::class]], function () {
    Route::post('/{id}', 'AppController@create');
    Route::put('/{id}', 'AppController@toggleActive');
    Route::delete('/{id}', 'AppController@delete');
});

Middleware::create(['prefix' => '/admin', 'middleware' => [Session::class, Admin::class]], function () {
    Route::get('', 'AdminController@view');
    Route::post('/invite', 'AdminController@invite', JSON::class);
    Route::put('/user/{id}', 'AdminController@userToggle');
    Route::delete('/history', 'AdminController@clearHistory');
});
