<?php

namespace App\Providers;

use App\Repositories\CaliberRepositoryEloquent;
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
        $this->app->bind(CaliberRepository::class, CaliberRepositoryEloquent::class);
    }
}
