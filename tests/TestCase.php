<?php

namespace Michal78\Tasks\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Michal78\Tasks\TasksServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        // run package migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Artisan::command('tasks:test-command {--model-type=} {--model-id=} {--flag=}', function () {
            app()->instance('task_test.last_command_model_type', (string) $this->option('model-type'));
            app()->instance('task_test.last_command_model_id', (string) $this->option('model-id'));
            app()->instance('task_test.last_command_flag', (string) $this->option('flag'));
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            TasksServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
