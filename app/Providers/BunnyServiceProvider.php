<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\BunnyCDNStorage;

class BunnyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\BunnyCDNStorage', function ($app) {
            return new BunnyCDNStorage();
        });
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
