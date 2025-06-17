<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () {
    return $router->app->version();
});

$router->get('/routes', function() use ($router) {
    return response()->json($router->getRoutes());
});

$router->get('barbers', 'BarberController@index');
$router->get('barbers/{id}', 'BarberController@show');
$router->get('services', 'ServiceController@index');
$router->get('services/{id}', 'ServiceController@show');

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
});

$router->group(['middleware' => 'auth'], function () use ($router) {    
    $router->get('auth/me', 'AuthController@me');
    $router->post('auth/logout', 'AuthController@logout');
    
    $router->get('available-slots', 'SchedulingController@availableSlots');

    $router->group(['prefix' => 'admin'], function () use ($router) {
        $router->get('scheduling/today', 'AdminController@todayScheduling');
        $router->get('scheduling/future', 'AdminController@futureScheduling');
        $router->get('scheduling/date/{date}', 'AdminController@SchedulingByDate');
    });
});
