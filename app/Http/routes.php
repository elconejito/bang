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
    return view('welcome');
});

Route::resource('firearms', 'FirearmController');

Route::resource('purposes', 'PurposeController');

Route::resource('ranges', 'RangeController');

Route::resource('stores', 'StoreController');

Route::resource('bullets', 'BulletController');

Route::resource('cartridges', 'CartridgeController');

Route::resource('orders', 'OrderController');

Route::resource('shoots', 'ShootController');