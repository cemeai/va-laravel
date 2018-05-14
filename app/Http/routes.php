<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return redirect('dashboard');
});

Route::auth();

Route::get('/dashboard', 'HomeController@dashboard');
Route::get('/test', 'HomeController@test');


Route::get('/get_cb_users', 'UserController@get_cb_users');
