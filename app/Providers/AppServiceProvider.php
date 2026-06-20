<?php

namespace App\Providers;

use App\Models\Light;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\Suppressor;
use App\Observers\AccessoryObserver;
use App\Observers\MagazineObserver;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Suppressor::observe(AccessoryObserver::class);
        Optic::observe(AccessoryObserver::class);
        Light::observe(AccessoryObserver::class);
        MiscAccessory::observe(AccessoryObserver::class);
        Magazine::observe(MagazineObserver::class);
    }

    public function register(): void
    {
        Fortify::ignoreRoutes();
    }
}
