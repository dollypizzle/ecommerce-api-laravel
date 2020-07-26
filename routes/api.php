<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'PassportController@login')->name('login');
Route::post('register', 'PassportController@register')->name('register');

Route::middleware('auth:api')->group(function () {
    Route::post('logout', 'PassportController@logout');
});

Route::get('user', 'PassportController@userdetails')->name('userdetails');

Route::get('/products', 'ProductController@index');
Route::post('/products', 'ProductController@store')->middleware('auth:api');
Route::get('/product/{product}', 'ProductController@show');
Route::patch('/product/{product}', 'ProductController@update')->middleware('auth:api');
Route::delete('/product/{product}', 'ProductController@destroy')->middleware('auth:api');
