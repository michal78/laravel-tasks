<?php

namespace Michal78\Tasks;

use Illuminate\Support\ServiceProvider;
use Michal78\Tasks\Commands\RunDueTasksCommand;
use Michal78\Tasks\Support\TaskRunner;

class TasksServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-tasks.php'),
            ], 'config');

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-tasks'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-tasks');

        $this->app->singleton(TaskRunner::class);

        // Register the main class to use with the facade
        $this->app->singleton('tasks', function ($app) {
            return new Tasks($app->make(TaskRunner::class));
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                RunDueTasksCommand::class,
            ]);
        }
    }
}
