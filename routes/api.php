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

Route::middleware('jwt')->group(function () {
    Route::resource('calibers', 'API\CaliberController');
    Route::resource('calibers.ammunition', 'API\AmmunitionController');

    // Reference Data
    Route::resource('caliber-type', 'API\Reference\CaliberTypeController');
});
