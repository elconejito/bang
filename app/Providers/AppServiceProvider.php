<?php

namespace App\Providers;

use App\Repositories\AmmunitionRepositoryEloquent;
use App\Repositories\CaliberRepositoryEloquent;
use App\Repositories\Interfaces\AmmunitionRepository;
use App\Repositories\Interfaces\CaliberRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AmmunitionRepository::class, AmmunitionRepositoryEloquent::class);
        $this->app->bind(CaliberRepository::class, CaliberRepositoryEloquent::class);
        $this->app->bind(
            \Auth0\Login\Contract\Auth0UserRepository::class,
            \App\Repositories\UserRepositoryAuth0::class
        );
    }
}
