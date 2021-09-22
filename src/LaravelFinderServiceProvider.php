<?php

namespace Backfron\LaravelFinder;

use Illuminate\Support\ServiceProvider;

class LaravelFinderServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'backfron');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'backfron');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-finder.php', 'laravel-finder');

        // Register the service the package provides.
        // $this->app->singleton('laravel-finder', function ($app) {
        //     return new LaravelFinder;
        // });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-finder'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravel-finder.php' => config_path('laravel-finder.php'),
        ], 'laravel-finder.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/backfron'),
        ], 'laravel-finder.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/backfron'),
        ], 'laravel-finder.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/backfron'),
        ], 'laravel-finder.views');*/

        // Registering package commands.
        $this->commands([
            \Backfron\LaravelFinder\Commands\MakeFinderCommand::class
        ]);
    }
}
