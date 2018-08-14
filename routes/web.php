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

$router->post('/message', 'MessageController@create');
$router->put('/message/{id}', 'MessageController@update');
$router->get('/message', 'MessageController@list');
$router->delete('/message/{id}', 'MessageController@delete');
$router->get('/message/{id}', 'MessageController@get');
$router->options('/message/{id}', 'MessageController@option');
$router->options('/message', 'MessageController@option');

$router->post('/authtoken','AuthTokenController@create');
$router->options('/authtoken','AuthTokenController@option');
$router->delete('/authtoken/{token}', 'AuthTokenController@delete');
$router->get('/authtoken/{token}','AuthTokenController@get');
$router->options('/authtoken/{token}','AuthTokenController@option');

$router->post('/administrator', 'AdminController@create');
$router->put('/administrator/{id}', 'AdminController@update');
$router->get('/administrator', 'AdminController@list');
$router->delete('/administrator/{id}', 'AdminController@delete');
$router->get('/administrator/{id}', 'AdminController@get');
$router->options('/administrator/{id}', 'AdminController@option');
$router->options('/administrator', 'AdminController@option');

$router->post('/event', 'EventController@create');
$router->put('/event/{id}', 'EventController@update');
$router->get('/event', 'EventController@list');
$router->delete('/event/{id}', 'EventController@delete');
$router->get('/event/{id}', 'EventController@get');
$router->options('/event/{id}', 'EventController@option');
$router->options('/event', 'EventController@option');

$router->post('/image', 'ImageController@create');
$router->put('/image/{id}', 'ImageController@update');
$router->get('/image', 'ImageController@list');
$router->delete('/image/{id}', 'ImageController@delete');
$router->get('/image/{id}', 'ImageController@get');
$router->options('/image/{id}', 'ImageController@option');
$router->options('/image', 'ImageController@option');

