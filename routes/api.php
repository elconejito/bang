<?php

use App\Http\Controllers\API\Ammunition\NoteController as AmmunitionNoteController;
use App\Http\Controllers\API\AmmunitionController;
use App\Http\Controllers\API\CaliberController;
use App\Http\Controllers\API\FirearmController;
use App\Http\Controllers\API\Firearms\NoteController as FirearmsNoteController;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\MagazineController;
use App\Http\Controllers\API\Reference\AmmunitionCasingController;
use App\Http\Controllers\API\Reference\AmmunitionConditionController;
use App\Http\Controllers\API\Reference\BulletTypeController;
use App\Http\Controllers\API\Reference\CaliberTypeController;
use App\Http\Controllers\API\Reference\LocationTypeController;
use App\Http\Controllers\API\Reference\PrimerTypeController;
use App\Http\Controllers\API\Reference\PurposeController;
use App\Http\Controllers\API\Reference\ShellLengthController;
use App\Http\Controllers\API\Reference\ShellTypeController;
use App\Http\Controllers\API\Reference\ShotMaterialController;
use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\TrainingController;
use App\Http\Controllers\Auth\AuthController;
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

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('reset', [AuthController::class, 'reset'])->name('password.reset');
});

Route::middleware('api')->group(function () {
    // CMS data resource controllers
    Route::resources([
        'calibers'            => CaliberController::class,
        'calibers.ammunition' => AmmunitionController::class,
        'firearms'            => FirearmController::class,
        'inventories'         => InventoryController::class,
        'locations'           => LocationController::class,
        'magazines'           => MagazineController::class,
        'stores'              => StoreController::class,
        'training'            => TrainingController::class,
    ]);

    // Extra routes
    Route::get('calibers/{caliber_id}/total', [CaliberController::class, 'total']);
    Route::get('ammunition/{ammunition_id}/total', [AmmunitionController::class, 'total']);

    // Notes resource routes
    Route::resources([
        'ammunition.notes' => AmmunitionNoteController::class,
        'firearms.notes'   => FirearmsNoteController::class,
    ]);

    // Reference Data resource routes
    Route::resources([
        'ammunition-casing'    => AmmunitionCasingController::class,
        'ammunition-condition' => AmmunitionConditionController::class,
        'bullet-type'          => BulletTypeController::class,
        'caliber-type'         => CaliberTypeController::class,
        'location-type'        => LocationTypeController::class,
        'purpose'              => PurposeController::class,
        'shell-length'         => ShellLengthController::class,
        'shell-type'           => ShellTypeController::class,
        'shot-material'        => ShotMaterialController::class,
    ]);
});
