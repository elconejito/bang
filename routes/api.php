<?php

use App\Http\Controllers\API\AccessoriesController;
use App\Http\Controllers\API\Ammunition\NoteController as AmmunitionNoteController;
use App\Http\Controllers\API\AmmunitionController;
use App\Http\Controllers\API\AmmunitionPictureController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CaliberController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\FirearmActivityController;
use App\Http\Controllers\API\FirearmController;
use App\Http\Controllers\API\FirearmPictureController;
use App\Http\Controllers\API\Firearms\NoteController as FirearmsNoteController;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\LightController;
use App\Http\Controllers\API\LightEventController;
use App\Http\Controllers\API\LightPictureController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\LocationPictureController;
use App\Http\Controllers\API\MagazineController;
use App\Http\Controllers\API\MagazineEventController;
use App\Http\Controllers\API\MagazinePictureController;
use App\Http\Controllers\API\MiscAccessoryController;
use App\Http\Controllers\API\MiscAccessoryEventController;
use App\Http\Controllers\API\MiscAccessoryPictureController;
use App\Http\Controllers\API\OpticController;
use App\Http\Controllers\API\OpticEventController;
use App\Http\Controllers\API\OpticPictureController;
use App\Http\Controllers\API\PictureController;
use App\Http\Controllers\API\RangeController;
use App\Http\Controllers\API\RangePictureController;
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
use App\Http\Controllers\API\StorePictureController;
use App\Http\Controllers\API\SuppressorController;
use App\Http\Controllers\API\SuppressorEventController;
use App\Http\Controllers\API\SuppressorPictureController;
use App\Http\Controllers\API\TargetController;
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
    Route::get('dashboard', [DashboardController::class, 'index']);

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
        'ranges' => RangeController::class,
        'stores' => StoreController::class,
        'suppressors' => SuppressorController::class,
        'training' => TrainingController::class,
    ]);

    Route::resource('training.lines', SessionLineController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['lines' => 'sessionLine']);

    Route::resource('training.targets', TargetController::class)
        ->only(['store', 'destroy'])
        ->parameters(['targets' => 'target']);

    Route::get('calibers/{caliber}/total', [CaliberController::class, 'total']);
    Route::get('ammunition/{ammunition}/total', [AmmunitionController::class, 'total']);
    Route::get('firearms/{firearm}/activity', [FirearmActivityController::class, 'index']);

    // Pictures — library
    Route::get('pictures', [PictureController::class, 'index']);
    Route::post('pictures', [PictureController::class, 'store']);

    // Pictures — per entity (reorder must come before {picture} wildcard in each group)
    foreach ([
        'ammunition' => [AmmunitionPictureController::class, 'ammunition'],
        'firearms' => [FirearmPictureController::class, 'firearm'],
        'lights' => [LightPictureController::class, 'light'],
        'locations' => [LocationPictureController::class, 'location'],
        'magazines' => [MagazinePictureController::class, 'magazine'],
        'misc-accessories' => [MiscAccessoryPictureController::class, 'misc_accessory'],
        'optics' => [OpticPictureController::class, 'optic'],
        'ranges' => [RangePictureController::class, 'range'],
        'stores' => [StorePictureController::class, 'store'],
        'suppressors' => [SuppressorPictureController::class, 'suppressor'],
    ] as $prefix => [$controller, $param]) {
        Route::get("{$prefix}/{{$param}}/pictures", [$controller, 'index']);
        Route::post("{$prefix}/{{$param}}/pictures", [$controller, 'store']);
        Route::patch("{$prefix}/{{$param}}/pictures/reorder", [$controller, 'reorder']);
        Route::post("{$prefix}/{{$param}}/pictures/{picture}/attach", [$controller, 'attach']);
        Route::patch("{$prefix}/{{$param}}/pictures/{picture}/primary", [$controller, 'setPrimary']);
        Route::delete("{$prefix}/{{$param}}/pictures/{picture}", [$controller, 'detach']);
    }

    // Accessory events (history)
    foreach ([
        'lights' => [LightEventController::class, 'light'],
        'magazines' => [MagazineEventController::class, 'magazine'],
        'misc-accessories' => [MiscAccessoryEventController::class, 'misc_accessory'],
        'optics' => [OpticEventController::class, 'optic'],
        'suppressors' => [SuppressorEventController::class, 'suppressor'],
    ] as $prefix => [$controller, $param]) {
        Route::get("{$prefix}/{{$param}}/events", [$controller, 'index']);
        Route::post("{$prefix}/{{$param}}/events", [$controller, 'store']);
    }

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
