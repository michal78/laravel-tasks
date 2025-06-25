<?php

namespace Michal78\Tasks;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Michal78\Tasks\Console\Commands\DispatchDueTasksCommand;

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

            // Register package command
            $this->commands([
                DispatchDueTasksCommand::class,
            ]);

            if (config('laravel-tasks.auto_schedule')) {
                $this->app->booted(function () {
                    $schedule = $this->app->make(Schedule::class);
                    $schedule->command('tasks:dispatch')->everyMinute();
                });
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-tasks');

        // Register the main class to use with the facade
        $this->app->singleton('tasks', function () {
            return new Tasks;
        });
    }
}
