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

/*
|--------------------------------------------------------------------------
| Orders Routes
|--------------------------------------------------------------------------
*/
// Filtered routes
Route::get('orders/stores/{id}', [
    'as' => 'ordersStores',
    'uses' => 'OrderController@showStores'
]);
Route::get('orders/bullets/{id}', [
    'as' => 'ordersBullets',
    'uses' => 'OrderController@showBullets'
]);
// Resource routes
Route::resource('orders', 'OrderController');

/*
|--------------------------------------------------------------------------
| Shoots Routes
|--------------------------------------------------------------------------
*/
// Filtered routes
Route::get('shoots/ranges/{id}', [
    'as' => 'shootsRanges',
    'uses' => 'ShootController@showRanges'
]);
Route::get('shoots/firearms/{id}', [
    'as' => 'shootsFirearms',
    'uses' => 'ShootController@showFirearms'
]);
Route::get('shoots/bullets/{id}', [
    'as' => 'shootsBullets',
    'uses' => 'ShootController@showBullets'
]);
// Resource routes
Route::resource('shoots', 'ShootController');