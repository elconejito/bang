<?php

namespace App\Providers;

use App\Repositories\AmmunitionCasingRepositoryEloquent;
use App\Repositories\AmmunitionConditionRepositoryEloquent;
use App\Repositories\AmmunitionRepositoryEloquent;
use App\Repositories\BulletTypeRepositoryEloquent;
use App\Repositories\CaliberRepositoryEloquent;
use App\Repositories\CaliberTypeRepositoryEloquent;
use App\Repositories\FirearmRepositoryEloquent;
use App\Repositories\Interfaces\AmmunitionCasingRepository;
use App\Repositories\Interfaces\AmmunitionConditionRepository;
use App\Repositories\Interfaces\AmmunitionRepository;
use App\Repositories\Interfaces\BulletTypeRepository;
use App\Repositories\Interfaces\CaliberRepository;
use App\Repositories\Interfaces\CaliberTypeRepository;
use App\Repositories\Interfaces\FirearmRepository;
use App\Repositories\Interfaces\InventoryRepository;
use App\Repositories\Interfaces\LocationRepository;
use App\Repositories\Interfaces\MagazineRepository;
use App\Repositories\Interfaces\NoteRepository;
use App\Repositories\Interfaces\PrimerTypeRepository;
use App\Repositories\Interfaces\PurposeRepository;
use App\Repositories\Interfaces\ShellLengthRepository;
use App\Repositories\Interfaces\ShellTypeRepository;
use App\Repositories\Interfaces\ShotMaterialRepository;
use App\Repositories\Interfaces\StoreRepository;
use App\Repositories\Interfaces\TrainingRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\InventoryRepositoryEloquent;
use App\Repositories\LocationRepositoryEloquent;
use App\Repositories\MagazineRepositoryEloquent;
use App\Repositories\NoteRepositoryEloquent;
use App\Repositories\PrimerTypeRepositoryEloquent;
use App\Repositories\PurposeRepositoryEloquent;
use App\Repositories\ShellLengthRepositoryEloquent;
use App\Repositories\ShellTypeRepositoryEloquent;
use App\Repositories\ShotMaterialRepositoryEloquent;
use App\Repositories\StoreRepositoryEloquent;
use App\Repositories\TrainingRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Model Repository Bindings
        $this->app->bind(AmmunitionRepository::class, AmmunitionRepositoryEloquent::class);
        $this->app->bind(CaliberRepository::class, CaliberRepositoryEloquent::class);
        $this->app->bind(FirearmRepository::class, FirearmRepositoryEloquent::class);
        $this->app->bind(InventoryRepository::class, InventoryRepositoryEloquent::class);
        $this->app->bind(LocationRepository::class, LocationRepositoryEloquent::class);
        $this->app->bind(MagazineRepository::class, MagazineRepositoryEloquent::class);
        $this->app->bind(NoteRepository::class, NoteRepositoryEloquent::class);
        $this->app->bind(StoreRepository::class, StoreRepositoryEloquent::class);
        $this->app->bind(TrainingRepository::class, TrainingRepositoryEloquent::class);
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);

        // Reference Repository Bindings
        $this->app->bind(AmmunitionCasingRepository::class, AmmunitionCasingRepositoryEloquent::class);
        $this->app->bind(AmmunitionConditionRepository::class, AmmunitionConditionRepositoryEloquent::class);
        $this->app->bind(BulletTypeRepository::class, BulletTypeRepositoryEloquent::class);
        $this->app->bind(CaliberTypeRepository::class, CaliberTypeRepositoryEloquent::class);
        $this->app->bind(PrimerTypeRepository::class, PrimerTypeRepositoryEloquent::class);
        $this->app->bind(PurposeRepository::class, PurposeRepositoryEloquent::class);
        $this->app->bind(ShellLengthRepository::class, ShellLengthRepositoryEloquent::class);
        $this->app->bind(ShellTypeRepository::class, ShellTypeRepositoryEloquent::class);
        $this->app->bind(ShotMaterialRepository::class, ShotMaterialRepositoryEloquent::class);

    }
}
