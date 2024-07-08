<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RMQ;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton('queue.connection', function ($app) {
            return new RMQ();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
