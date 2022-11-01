<?php

namespace SwooInc\BeCool;

use Illuminate\Support\ServiceProvider;

class BeCoolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('becool.php'),
            ], 'config');
        }

        $this->app->bind(Client::class, static function () {
            return new Client(
                config('becool.api.key'),
                config('becool.sandbox')
            );
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'becool');
    }
}
