<?php

namespace TaylorNetwork\API;

use Illuminate\Support\ServiceProvider;

class APIServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/api.php' => config_path('api.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/api.php', 'api'
        );
        
        $this->commands([ Commands\DriverMakeCommand::class ]);

        $this->app->bind('API', function () {
            return new API();
        });
    }
}
