<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('register','PublicController@register')->name('register');
Route::match(['get', 'post'], 'login', 'PublicController@login')->name('login');


//Route::get('login','PublicController@login')->name('login');

//Route::post('login','PublicController@login')->name('login');

Route::group(['middleware' => ['user.auth']], function () {
	Route::get('/', 'IndexController@index')->name('home');
});


