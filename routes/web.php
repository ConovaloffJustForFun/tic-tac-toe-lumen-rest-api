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

$router->get('/', ['uses' => 'MainPageController@index']);

$router->group(['prefix' => 'api'], function ($router) {
    $router->post('table', 'GameTableController@createTable');
    $router->get('table/{id}', 'GameTableController@getTable');
    $router->put('table/{id}', 'GameTableController@updateTable');
    $router->post('table/{id}', 'GameTableController@updateTable');
});
