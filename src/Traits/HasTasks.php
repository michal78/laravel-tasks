<?php

namespace Michal78\Tasks\Traits;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Michal78\Tasks\Models\Task;

trait HasTasks
{
    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function addTask(array $attributes): Task
    {
        return $this->tasks()->create(array_merge([
            'status' => Task::STATUS_PENDING,
        ], $attributes));
    }

    public function scheduleCommandTask(
        string $name,
        string $command,
        DateTimeInterface|string $runAt,
        array $payload = [],
    ): Task {
        return $this->addTask([
            'name' => $name,
            'type' => Task::TYPE_COMMAND,
            'target' => $command,
            'payload' => $payload,
            'run_at' => $runAt,
        ]);
    }

    public function scheduleActionTask(
        string $name,
        string $actionClass,
        DateTimeInterface|string $runAt,
        array $payload = [],
        ?string $method = null,
    ): Task {
        return $this->addTask([
            'name' => $name,
            'type' => Task::TYPE_ACTION,
            'target' => $actionClass,
            'method' => $method,
            'payload' => $payload,
            'run_at' => $runAt,
        ]);
    }

    public function scheduleEventTask(
        string $name,
        string $eventClass,
        DateTimeInterface|string $runAt,
        array $payload = [],
    ): Task {
        return $this->addTask([
            'name' => $name,
            'type' => Task::TYPE_EVENT,
            'target' => $eventClass,
            'payload' => $payload,
            'run_at' => $runAt,
        ]);
    }

    public function scheduleServiceTask(
        string $name,
        string $serviceClass,
        DateTimeInterface|string $runAt,
        array $payload = [],
        string $method = 'handle',
    ): Task {
        return $this->addTask([
            'name' => $name,
            'type' => Task::TYPE_SERVICE,
            'target' => $serviceClass,
            'method' => $method,
            'payload' => $payload,
            'run_at' => $runAt,
        ]);
    }

    public function pendingTasks()
    {
        return $this->tasks()->where('status', Task::STATUS_PENDING)->get();
    }

    public function completedTasks()
    {
        return $this->tasks()->where('status', Task::STATUS_SUCCEEDED)->get();
    }
}
