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

// DEFUALT
Route::get('/', function () {
    return redirect('dashboard');
});

// LOGINS / REGISTER
// Route::get('test', 'HomeController@test');
Route::get('googleLogin', 'UserController@googleLogin');
Route::auth();

// MENU
Route::get('dashboard', 'HomeController@dashboard');
Route::get('billing', 'HomeController@billing');
Route::get('api_login', function () {
	print_r($_GET['auth_session_id']);
	echo '<br>';
	print_r($_GET['auth_session_token']);
});

// ACTIONS
Route::get('/get_cb_users', 'UserController@get_cb_users');
Route::post('/update_billing', 'UserController@update_billing');
Route::post('/update_payment', 'UserController@update_payment');