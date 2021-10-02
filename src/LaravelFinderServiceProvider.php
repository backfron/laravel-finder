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

        // Registering package commands.
        $this->commands([
            \Backfron\LaravelFinder\Commands\MakeFinderCommand::class,
            \Backfron\LaravelFinder\Commands\MakeFilterCommand::class,
        ]);
    }
}
