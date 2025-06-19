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

$router->get('/', fn() => $router->app->version());

$router->get('/routes', function() use ($router) {
    return response()->json($router->getRoutes());
});

$router->get('barbers', 'BarberController@index');
$router->get('barbers/{id}', 'BarberController@show');
$router->get('barbers/specialty/{specialtyId}', 'BarberController@getBySpecialty');

$router->get('services', 'ServiceController@index');
$router->get('services/{id}', 'ServiceController@show');

$router->get('specialties', 'SpecialtyController@index');
$router->get('specialties/{id}', 'SpecialtyController@show');

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
});

$router->group(['middleware' => 'auth'], function () use ($router) {    
    $router->get('auth/me', 'AuthController@me');
    $router->post('auth/logout', 'AuthController@logout');

    $router->group(['prefix' => 'scheduling'], function () use ($router) {
        $router->get('/', 'SchedulingController@index');           
        $router->post('/', 'SchedulingController@store');          
        $router->get('{id}', 'SchedulingController@show');         
        $router->put('{id}', 'SchedulingController@update');       
    });
    
    $router->get('available-slots', 'SchedulingController@availableSlots');
    $router->get('barbers-by-specialty', 'SchedulingController@getBarbersBySpecialty');

    $router->group(['prefix' => 'admin', 'middelware'=>'admin'], function () use ($router) {

        $router->get('scheduling/today', 'AdminController@todayScheduling');
        $router->get('scheduling/future', 'AdminController@futureScheduling');
        $router->get('scheduling/date/{date}', 'AdminController@schedulingByDate');
        $router->get('scheduling/report', 'AdminController@schedulingReport');
        
        $router->put('scheduling/{id}/confirm', 'AdminController@confirmScheduling');
        $router->put('scheduling/{id}/complete', 'AdminController@completeScheduling');
        $router->put('scheduling/{id}/cancel', 'AdminController@cancelScheduling');

        $router->post('barbers', 'BarberController@store'); 
        $router->put('barbers/{id}', 'BarberController@update');
        $router->post('barbers/{id}/specialties', 'BarberController@addSpecialty');
        $router->delete('barbers/{id}/specialties', 'BarberController@removeSpecialty');

        $router->post('specialties', 'SpecialtyController@store');
        $router->put('specialties/{id}', 'SpecialtyController@update');
        $router->delete('specialties/{id}', 'SpecialtyController@destroy');

        $router->post('services', 'ServiceController@store');
        $router->put('services/{id}', 'ServiceController@update');
        $router->delete('services/{id}', 'ServiceController@destroy');
    });
});
