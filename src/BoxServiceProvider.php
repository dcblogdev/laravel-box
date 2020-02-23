<?php

namespace Dcblogdev\Box;

use Illuminate\Support\ServiceProvider;

class BoxServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {

            // Publishing the configuration file.
            $this->publishes([
                __DIR__.'/../config/box.php' => config_path('box.php'),
            ], 'config');

            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/database/migrations/create_box_tokens_table.php' => $this->app->databasePath()."/migrations/{$timestamp}_create_box_tokens_table.php",
            ], 'migrations');
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/box.php', 'box');

        // Register the service the package provides.
        $this->app->singleton('box', function ($app) {
            return new Box;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['box'];
    }
}
