<?php

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

$router->get('/', function () use ($router) {
    return redirect('/swagger-ui/', 301);
});

$router->get('/swagger', 'SwaggerController@index');

$router->post('/user', 'UserController@create');
$router->put('/user/{id}', 'UserController@update');
$router->get('/user', 'UserController@list');
$router->delete('/user/{id}', 'UserController@delete');
$router->get('/user/{id}', 'UserController@get');
$router->options('/user/{id}', 'UserController@option');
$router->options('/user', 'UserController@option');
