<?php

use App\Http\Controllers\API\AmmunitionController;
use App\Http\Controllers\API\CaliberController;
use Illuminate\Support\Facades\Route;

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
    Route::get('calibers/{caliber_id}/total', [CaliberController::class, 'total']);
    Route::get('ammunition/{ammunition_id}/total', [AmmunitionController::class, 'total']);

    Route::resource('firearms', 'API\FirearmController');
    Route::resource('inventories', 'API\InventoryController');
    Route::resource('locations', 'API\LocationController');
    Route::resource('magazines', 'API\MagazineController');
    Route::resource('training', 'API\TrainingController');

    // Notes resource routes
    Route::resource('ammunition.notes', 'API\Ammunition\NoteController');
    Route::resource('firearms.notes', 'API\Firearms\NoteController');

    // Reference Data
    Route::resource('ammunition-casing', 'API\Reference\AmmunitionCasingController');
    Route::resource('ammunition-condition', 'API\Reference\AmmunitionConditionController');
    Route::resource('bullet-type', 'API\Reference\BulletTypeController');
    Route::resource('caliber-type', 'API\Reference\CaliberTypeController');
    Route::resource('primer-type', 'API\Reference\PrimerTypeController');
    Route::resource('purpose', 'API\Reference\PurposeController');
    Route::resource('shell-length', 'API\Reference\ShellLengthController');
    Route::resource('shell-type', 'API\Reference\ShellTypeController');
    Route::resource('shot-material', 'API\Reference\ShotMaterialController');
});
