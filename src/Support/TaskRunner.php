<?php

namespace Michal78\Tasks\Support;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Michal78\Tasks\Models\Task;
use Michal78\Tasks\Models\TaskLog;
use Throwable;

class TaskRunner
{
    public function __construct(
        protected Container $container,
    ) {
    }

    /**
     * @throws BindingResolutionException
     */
    public function run(Task $task): void
    {
        $task->forceFill([
            'status' => Task::STATUS_RUNNING,
        ])->save();

        $log = $this->createLog($task);

        try {
            $this->execute($task);

            $task->forceFill([
                'status' => Task::STATUS_SUCCEEDED,
                'last_ran_at' => now(),
                'error_message' => null,
            ])->save();

            $this->finishLog($log, Task::STATUS_SUCCEEDED);
        } catch (Throwable $exception) {
            $task->forceFill([
                'status' => Task::STATUS_FAILED,
                'last_ran_at' => now(),
                'error_message' => $exception->getMessage(),
            ])->save();

            $this->finishLog($log, Task::STATUS_FAILED, $exception->getMessage());

            throw $exception;
        }
    }

    /**
     * @throws BindingResolutionException
     */
    protected function execute(Task $task): void
    {
        $model = $task->taskable;
        $payload = $task->payload ?? [];

        match ($task->type) {
            Task::TYPE_COMMAND => $this->runCommand($task, $model, $payload),
            Task::TYPE_ACTION => $this->runAction($task, $model, $payload),
            Task::TYPE_EVENT => $this->runEvent($task, $model, $payload),
            Task::TYPE_SERVICE => $this->runService($task, $model, $payload),
            default => throw new \RuntimeException("Unsupported task type [{$task->type}]"),
        };
    }

    protected function runCommand(Task $task, object $model, array $payload): void
    {
        Artisan::call($task->target, array_merge($payload, [
            '--model-type' => $model::class,
            '--model-id' => (string) $model->getKey(),
        ]));
    }

    /**
     * @throws BindingResolutionException
     */
    protected function runAction(Task $task, object $model, array $payload): void
    {
        $action = $this->container->make($task->target);
        $method = $task->method ?? '__invoke';

        $this->container->call([$action, $method], [
            'model' => $model,
            'payload' => $payload,
            'task' => $task,
        ]);
    }

    protected function runEvent(Task $task, object $model, array $payload): void
    {
        $event = new $task->target($model, $payload, $task);
        Event::dispatch($event);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function runService(Task $task, object $model, array $payload): void
    {
        $service = $this->container->make($task->target);
        $method = $task->method ?: 'handle';

        $this->container->call([$service, $method], [
            'model' => $model,
            'payload' => $payload,
            'task' => $task,
        ]);
    }

    protected function createLog(Task $task): ?TaskLog
    {
        if (! config('laravel-tasks.logging.enabled')) {
            return null;
        }

        return $task->logs()->create([
            'status' => Task::STATUS_RUNNING,
            'started_at' => now(),
        ]);
    }

    protected function finishLog(?TaskLog $log, string $status, ?string $errorMessage = null): void
    {
        if (! $log) {
            return;
        }

        $log->forceFill([
            'status' => $status,
            'finished_at' => now(),
            'error_message' => $errorMessage,
        ])->save();
    }
}
