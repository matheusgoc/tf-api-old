<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function (Router $router) {

    $router->post('login', 'AuthController@login');
    $router->get('logout', 'AuthController@logout');
    $router->get('refresh', 'AuthController@refresh');
    $router->get('me', 'AuthController@me');
});

Route::apiResource('customer', 'CustomerController');
Route::post('user/{user}/customer', 'CustomerController@storeByUser');

Route::group(['middleware'=>'auth:api'], function($router) {

    Route::apiResource('agent', 'AgentController');
});
