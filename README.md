# Laravel simple Tasks

This package provides simple task management for Laravel applications. It is
compatible with Laravel versions 8 through 12.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/michal78/laravel-tasks.svg?style=flat-square)](https://packagist.org/packages/michal78/laravel-tasks)
[![Total Downloads](https://img.shields.io/packagist/dt/michal78/laravel-tasks.svg?style=flat-square)](https://packagist.org/packages/michal78/laravel-tasks)
![GitHub Actions](https://github.com/michal78/laravel-tasks/actions/workflows/main.yml/badge.svg)

## Installation

You can install the package via composer:

```bash
composer require michal78/laravel-tasks
```

## Usage

```php
// Add the HasTasks trait to your model
use Michal78\LaravelTasks\Traits\HasTasks;

class User extends Model
{
    use HasTasks;
}

// Create a task
$user->addTask(
    [
        'name' => 'My task',
        'description' => 'My task description',
        'priority' => 2,
        'due_date' => now()->addDays(7),
    ]
);

// Get all tasks
$user->tasks;

// Get all tasks that are not completed
$user->tasks()->notCompleted()->get();

// Get all tasks that are completed
$user->tasks()->completed()->get();

// Get all tasks that are completed and have a due date in the future
$user->tasks()->completed()->future()->get();
```

```php
```

### Testing (Not implemented yet)

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email michal.skogemann@gmail.com instead of using the issue tracker.

## Credits

-   [Michal Skogemann](https://github.com/michal78)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
