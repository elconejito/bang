<?php

use App\Enums\NotableType;
use App\Http\Controllers\API\AccessoriesController;
use App\Http\Controllers\API\AmmunitionController;
use App\Http\Controllers\API\AmmunitionPictureController;
use App\Http\Controllers\API\AssetLifecycleController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CaliberController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\FirearmActivityController;
use App\Http\Controllers\API\FirearmController;
use App\Http\Controllers\API\FirearmLifecycleController;
use App\Http\Controllers\API\FirearmPictureController;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\LightController;
use App\Http\Controllers\API\LightEventController;
use App\Http\Controllers\API\LightPictureController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\LocationPictureController;
use App\Http\Controllers\API\MagazineBatchController;
use App\Http\Controllers\API\MagazineController;
use App\Http\Controllers\API\MagazineEventController;
use App\Http\Controllers\API\MagazineGroupController;
use App\Http\Controllers\API\MagazinePictureController;
use App\Http\Controllers\API\MiscAccessoryController;
use App\Http\Controllers\API\MiscAccessoryEventController;
use App\Http\Controllers\API\MiscAccessoryPictureController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\OpticController;
use App\Http\Controllers\API\OpticEventController;
use App\Http\Controllers\API\OpticPictureController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PasswordResetController;
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
use Illuminate\Support\Facades\Storage;

Route::get('storage/pictures/{path}', function (string $path) {
    abort_unless(Storage::disk('pictures')->exists($path), 404);

    return response()->file(Storage::disk('pictures')->path($path));
})->where('path', '.*')->middleware('signed')->name('storage.pictures');

// Auth routes — public
Route::prefix('auth')->group(function () {
    Route::get('configuration', [AuthController::class, 'configuration']);
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::post('forgot-password', [PasswordResetController::class, 'store'])->middleware('throttle:3,1');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:6,1');

    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('throttle:6,1');

    Route::middleware(['auth:api', 'jwt.identity'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Protected resource routes
Route::middleware(['auth:api', 'jwt.identity'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::get('accessories', [AccessoriesController::class, 'index']);
    Route::get('magazine-groups', [MagazineGroupController::class, 'index']);
    Route::get('magazine-groups/{group}/magazines', [MagazineGroupController::class, 'magazines'])->whereNumber('group');
    Route::post('magazine-batches', [MagazineBatchController::class, 'store']);

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
        'orders' => OrderController::class,
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
    Route::post('firearms/{firearm}/archive', [FirearmLifecycleController::class, 'archive']);
    Route::post('firearms/{firearm}/unarchive', [FirearmLifecycleController::class, 'unarchive']);
    Route::patch('magazines/{magazine}/state', [MagazineController::class, 'changeState']);

    Route::post('suppressors/{suppressor}/archive', [AssetLifecycleController::class, 'archiveSuppressor']);
    Route::post('suppressors/{suppressor}/unarchive', [AssetLifecycleController::class, 'unarchiveSuppressor']);
    Route::post('optics/{optic}/archive', [AssetLifecycleController::class, 'archiveOptic']);
    Route::post('optics/{optic}/unarchive', [AssetLifecycleController::class, 'unarchiveOptic']);
    Route::post('lights/{light}/archive', [AssetLifecycleController::class, 'archiveLight']);
    Route::post('lights/{light}/unarchive', [AssetLifecycleController::class, 'unarchiveLight']);
    Route::post('misc-accessories/{misc_accessory}/archive', [AssetLifecycleController::class, 'archiveMiscAccessory']);
    Route::post('misc-accessories/{misc_accessory}/unarchive', [AssetLifecycleController::class, 'unarchiveMiscAccessory']);
    Route::post('magazines/{magazine}/archive', [AssetLifecycleController::class, 'archiveMagazine']);
    Route::post('magazines/{magazine}/unarchive', [AssetLifecycleController::class, 'unarchiveMagazine']);

    // Pictures — library
    Route::get('pictures', [PictureController::class, 'index']);
    Route::post('pictures', [PictureController::class, 'store']);
    Route::get('pictures/{picture}/urls', [PictureController::class, 'urls']);
    Route::delete('pictures/{picture}', [PictureController::class, 'destroy']);

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

    Route::get('{notableType}/{notable}/notes', [NoteController::class, 'index'])
        ->where('notableType', NotableType::routePattern())
        ->whereNumber('notable');
    Route::post('{notableType}/{notable}/notes', [NoteController::class, 'store'])
        ->where('notableType', NotableType::routePattern())
        ->whereNumber('notable');

    Route::apiResources([
        'ammunition-casing' => AmmunitionCasingController::class,
        'ammunition-condition' => AmmunitionConditionController::class,
        'bullet-type' => BulletTypeController::class,
        'caliber-type' => CaliberTypeController::class,
        'location-type' => LocationTypeController::class,
        'primer-type' => PrimerTypeController::class,
        'shell-length' => ShellLengthController::class,
        'shell-type' => ShellTypeController::class,
        'shot-material' => ShotMaterialController::class,
    ], ['only' => ['index']]);
    Route::apiResource('purpose', PurposeController::class)->except('show');
});
