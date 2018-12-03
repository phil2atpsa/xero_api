<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\XeroService;

class XeroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Services\XeroService', function ($app) {
            return new XeroService();
        });
    }
}
