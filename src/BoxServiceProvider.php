<?php

namespace DaveismynameLaravel\Box;

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
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'daveismynamelaravel');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'daveismynamelaravel');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $configPath = __DIR__.'/../config/box.php';
        $this->mergeConfigFrom($configPath, 'box');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {

            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/create_box_tokens_table.php' => $this->app->databasePath() . "/migrations/{$timestamp}_create_box_tokens_tables.php",
            ], 'migrations');

            $this->publishes([
                $configPath => config_path('box.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/daveismynamelaravel'),
            ], 'box.views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/daveismynamelaravel'),
            ], 'box.views');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/daveismynamelaravel'),
            ], 'box.views');*/

            // Registering package commands.
            // $this->commands([]);
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
