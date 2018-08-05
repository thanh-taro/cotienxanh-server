<?php

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

Route::name('api.')->namespace('API')->group(function () {
    Route::name('auth.')->group(function () {
        Route::post('register', 'AuthController@register')->name('register');
        Route::post('login', 'AuthController@login')->name('login');

        Route::middleware('auth:api')->group(function () {
            Route::post('logout', 'AuthController@logout')->name('logout');
            Route::get('me', 'AuthController@me')->name('me');
        });
    });
});
