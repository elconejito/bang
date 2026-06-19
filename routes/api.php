<?php

use App\Http\Controllers\API\AccessoriesController;
use App\Http\Controllers\API\Ammunition\NoteController as AmmunitionNoteController;
use App\Http\Controllers\API\AmmunitionController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CaliberController;
use App\Http\Controllers\API\FirearmActivityController;
use App\Http\Controllers\API\FirearmController;
use App\Http\Controllers\API\Firearms\NoteController as FirearmsNoteController;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\LightController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\MagazineController;
use App\Http\Controllers\API\MiscAccessoryController;
use App\Http\Controllers\API\OpticController;
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
use App\Http\Controllers\API\SessionLineController;
use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\SuppressorController;
use App\Http\Controllers\API\TrainingController;
use Illuminate\Support\Facades\Route;

// Auth routes — public
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    if (config('app.registration_enabled')) {
        Route::post('register', [AuthController::class, 'register']);
    }

    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('throttle:6,1');

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Protected resource routes
Route::middleware('auth:api')->group(function () {
    Route::get('accessories', [AccessoriesController::class, 'index']);

    Route::get('training/stats', [TrainingController::class, 'stats']);

    Route::resources([
        'ammunition' => AmmunitionController::class,
        'calibers' => CaliberController::class,
        'firearms' => FirearmController::class,
        'inventories' => InventoryController::class,
        'lights' => LightController::class,
        'locations' => LocationController::class,
        'magazines' => MagazineController::class,
        'misc-accessories' => MiscAccessoryController::class,
        'optics' => OpticController::class,
        'stores' => StoreController::class,
        'suppressors' => SuppressorController::class,
        'training' => TrainingController::class,
    ]);

    Route::resource('training.lines', SessionLineController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['lines' => 'sessionLine']);

    Route::get('calibers/{caliber}/total', [CaliberController::class, 'total']);
    Route::get('ammunition/{ammunition}/total', [AmmunitionController::class, 'total']);
    Route::get('firearms/{firearm}/activity', [FirearmActivityController::class, 'index']);

    Route::resources([
        'ammunition.notes' => AmmunitionNoteController::class,
        'firearms.notes' => FirearmsNoteController::class,
    ]);

    Route::resources([
        'ammunition-casing' => AmmunitionCasingController::class,
        'ammunition-condition' => AmmunitionConditionController::class,
        'bullet-type' => BulletTypeController::class,
        'caliber-type' => CaliberTypeController::class,
        'location-type' => LocationTypeController::class,
        'primer-type' => PrimerTypeController::class,
        'purpose' => PurposeController::class,
        'shell-length' => ShellLengthController::class,
        'shell-type' => ShellTypeController::class,
        'shot-material' => ShotMaterialController::class,
    ]);
});
