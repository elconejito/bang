<?php

use App\Http\Controllers\AmmunitionController;
use App\Http\Controllers\CaliberController;

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

Auth::routes();

Route::get('/', ['as' => 'home', function () {
    return view('welcome');
}]);

Route::get('/home', function () {
    return view('home');
});


/*
|--------------------------------------------------------------------------
| Auth Middleware
|--------------------------------------------------------------------------
|
| Wrap all the routes in Auth middleware
|
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Caliber Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('calibers')->group(function () {

    });

    /*
    |--------------------------------------------------------------------------
    | Magazine Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('magazines')->group(function () {
        Route::post('{magazine}/photos', 'MagazineController@addPhoto');
    });


/*
|--------------------------------------------------------------------------
| Ammunition Routes
|--------------------------------------------------------------------------
*/
// Resource routes
//Route::resource('calibers.ammunition', 'AmmunitionController');


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
Route::prefix('shoots')->group(function () {
    // Filtered routes
    Route::get('ranges/{id}', [
        'as' => 'shootsRanges',
        'uses' => 'ShootController@showRanges'
    ]);
    Route::get('firearms/{id}', [
        'as' => 'shootsFirearms',
        'uses' => 'ShootController@showFirearms'
    ]);
    Route::get('bullets/{id}', [
        'as' => 'shootsBullets',
        'uses' => 'ShootController@showBullets'
    ]);
});
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
    'uses' => 'TrainingController@showRanges'
]);
// Resource routes
Route::resource('trips/{trip}/targets', 'TargetController', [
    'names' => [
        'create' => 'trips.targets.create'
    ],
    'only' => [
        'create'
    ]
]);
Route::resource('training', 'TrainingController');


    /*
    |--------------------------------------------------------------------------
    | Resource Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('calibers', 'CaliberController');
    Route::resource('firearms', 'FirearmController');
    Route::resource('magazines', 'MagazineController');
    Route::resource('purposes', 'PurposeController');
    Route::resource('ranges', 'RangeController');
    Route::resource('stores', 'StoreController');
    Route::resource('targets', 'TargetController');
    // Nested Resource Route
    Route::resource('calibers.ammunitions', 'AmmunitionController');

});
