# Laravel Tasks

`michal78/laravel-tasks` lets you attach scheduled tasks to any Eloquent model.

A task can run one of four target types at a specific time:

- Artisan command
- Action class
- Event class
- Service class method

The model is always passed into the target, and task runs can be logged (optional).

[![Latest Version on Packagist](https://img.shields.io/packagist/v/michal78/laravel-tasks.svg?style=flat-square)](https://packagist.org/packages/michal78/laravel-tasks)
[![Total Downloads](https://img.shields.io/packagist/dt/michal78/laravel-tasks.svg?style=flat-square)](https://packagist.org/packages/michal78/laravel-tasks)

## Requirements

- PHP 8.2+ (Laravel 13 requires PHP 8.3+)
- Laravel 11, 12, or 13

## Installation

```bash
composer require michal78/laravel-tasks
```

Run migrations:

```bash
php artisan migrate
```

Optional config publish:

```bash
php artisan vendor:publish --provider="Michal78\Tasks\TasksServiceProvider" --tag=config
```

## Model Setup

Add the trait to any model:

```php
use Illuminate\Database\Eloquent\Model;
use Michal78\Tasks\Traits\HasTasks;

class User extends Model
{
    use HasTasks;
}
```

## Scheduling Tasks

### Command task

```php
$user->scheduleCommandTask(
    name: 'Sync user',
    command: 'users:sync',
    runAt: now()->addMinutes(10),
    payload: ['--force' => true],
);
```

The package injects these command options automatically:

- `--model-type` (model class)
- `--model-id` (model primary key)

### Action task

```php
$user->scheduleActionTask(
    name: 'Run action',
    actionClass: \App\Actions\UserAction::class,
    runAt: now()->addHour(),
    payload: ['source' => 'onboarding'],
);
```

Default method is `__invoke`. You can pass a custom method with the `method` argument.

### Event task

```php
$user->scheduleEventTask(
    name: 'Dispatch event',
    eventClass: \App\Events\UserTaskDue::class,
    runAt: now()->addMinutes(30),
    payload: ['channel' => 'email'],
);
```

Event constructor signature should accept:

```php
public function __construct(Model $model, array $payload, Task $task)
```

### Service task

```php
$user->scheduleServiceTask(
    name: 'Call service',
    serviceClass: \App\Services\UserTaskService::class,
    runAt: now()->addDay(),
    payload: ['dry_run' => false],
    method: 'handle', // optional, defaults to handle
);
```

## Running Due Tasks

The package registers:

```bash
php artisan tasks:run-due
```

Add it to Laravel scheduler:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('tasks:run-due')->everyMinute();
```

## Task Logging (Optional)

Each run can be logged in `task_logs`.

Config:

```php
// config/laravel-tasks.php
return [
    'logging' => [
        'enabled' => env('TASKS_LOGGING_ENABLED', true),
    ],
];
```

When enabled, each task run stores status (`running`, `succeeded`, `failed`), start/end timestamps, and optional error message.

## Testing

```bash
composer test
```

## License

MIT
