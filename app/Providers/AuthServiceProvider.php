<?php

namespace App\Providers;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Cartridge;
use App\Models\Firearm;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\Note;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Range;
use App\Models\Store;
use App\Models\Target;
use App\Models\TrainingSession;
use App\Policies\AmmunitionPolicy;
use App\Policies\CaliberPolicy;
use App\Policies\CartridgePolicy;
use App\Policies\FirearmPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\LocationPolicy;
use App\Policies\MagazinePolicy;
use App\Policies\NotePolicy;
use App\Policies\OrderPolicy;
use App\Policies\PurchasePolicy;
use App\Policies\RangePolicy;
use App\Policies\StorePolicy;
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
        Cartridge::class => CartridgePolicy::class,
        Firearm::class => FirearmPolicy::class,
        Inventory::class => InventoryPolicy::class,
        Location::class => LocationPolicy::class,
        Magazine::class => MagazinePolicy::class,
        Note::class => NotePolicy::class,
        Order::class => OrderPolicy::class,
        Purchase::class => PurchasePolicy::class,
        Range::class => RangePolicy::class,
        Store::class => StorePolicy::class,
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
