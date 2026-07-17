<?php

namespace App\Providers;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Models\Inventory;
use App\Models\Light;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\Order;
use App\Models\Picture;
use App\Models\Range;
use App\Models\SessionLine;
use App\Models\Store;
use App\Models\Suppressor;
use App\Models\Target;
use App\Models\TrainingSession;
use App\Policies\AmmunitionPolicy;
use App\Policies\CaliberPolicy;
use App\Policies\FirearmPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\LightPolicy;
use App\Policies\LocationPolicy;
use App\Policies\MagazinePolicy;
use App\Policies\MiscAccessoryPolicy;
use App\Policies\OpticPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PicturePolicy;
use App\Policies\RangePolicy;
use App\Policies\SessionLinePolicy;
use App\Policies\StorePolicy;
use App\Policies\SuppressorPolicy;
use App\Policies\TargetPolicy;
use App\Policies\TrainingSessionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Ammunition::class => AmmunitionPolicy::class,
        Caliber::class => CaliberPolicy::class,
        Firearm::class => FirearmPolicy::class,
        Inventory::class => InventoryPolicy::class,
        Location::class => LocationPolicy::class,
        Light::class => LightPolicy::class,
        Magazine::class => MagazinePolicy::class,
        MiscAccessory::class => MiscAccessoryPolicy::class,
        Optic::class => OpticPolicy::class,
        Order::class => OrderPolicy::class,
        Picture::class => PicturePolicy::class,
        Range::class => RangePolicy::class,
        SessionLine::class => SessionLinePolicy::class,
        Store::class => StorePolicy::class,
        Suppressor::class => SuppressorPolicy::class,
        Target::class => TargetPolicy::class,
        TrainingSession::class => TrainingSessionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot() {}
}
