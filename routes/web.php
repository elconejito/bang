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

Route::get('/', ['as' => 'home', function () {
    return view('welcome');
}]);

Route::resource('firearms', 'FirearmController');

Route::resource('magazines', 'MagazineController');

Route::resource('purposes', 'PurposeController');

Route::resource('ranges', 'RangeController');

Route::resource('stores', 'StoreController');

Route::resource('cartridges', 'CartridgeController');

/*
|--------------------------------------------------------------------------
| Bullet Routes
|--------------------------------------------------------------------------
*/
// Filtered routes
/*Route::get('bullets/cartridges/{id}', [
    'as' => 'bulletsCartridges',
    'uses' => 'BulletController@showCartridges'
]);
Route::get('bullets/purposes/{id}', [
    'as' => 'bulletsPurposes',
    'uses' => 'BulletController@showPurposes'
]);
Route::get('bullets/weights/{id}', [
    'as' => 'bulletsWeights',
    'uses' => 'BulletController@showWeights'
]);
Route::get('bullets/manufacturers/{id}', [
    'as' => 'bulletsManufacturers',
    'uses' => 'BulletController@showManufacturers'
]);*/
// Resource routes
Route::resource('cartridges.bullets', 'BulletController');

/*
|--------------------------------------------------------------------------
| Order Routes
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
| Inventory Routes
|--------------------------------------------------------------------------
*/
// Resource routes
Route::resource('orders.inventories', 'InventoryController');

/*
|--------------------------------------------------------------------------
| Shoot Routes
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
Route::resource('trips.shoots', 'ShootController');

/*
|--------------------------------------------------------------------------
| Trip Routes
|--------------------------------------------------------------------------
*/
// Filtered routes
Route::get('trips/ranges/{id}', [
    'as' => 'tripsRanges',
    'uses' => 'TripController@showRanges'
]);
// Resource routes
Route::resource('trips', 'TripController');
