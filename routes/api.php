<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//AUTH
Route::post('/login', 'Api\Auth\LoginController@login');
Route::post('/refresh', 'Api\Auth\LoginController@refresh');
Route::post('/register', 'Api\Auth\RegisterController@register');
Route::get('/unauthorized', 'Api\Auth\UserController@unauthorized');


Route::middleware('auth:api')->group(function () {
    
    //AUTH
    Route::post('/logout', 'Api\Auth\UserController@logout');
    Route::post('/user', 'Api\Auth\UserController@detailsUser');
    

});

/* Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signUp');

    Route::get('/test', 'Api\Auth\RegisterController@test');


    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
}); */
