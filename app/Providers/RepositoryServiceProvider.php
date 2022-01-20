<?php

namespace App\Providers;

use App\Data\Repositories\User\UserRepository;
use App\Data\Repositories\User\UserRepositoryInterface;
use App\Data\Repositories\Product\ProductRepository;
use App\Data\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
