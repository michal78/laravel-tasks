<?php

namespace Michal78\Tasks;

use Illuminate\Database\Eloquent\Collection;
use Michal78\Tasks\Models\Task;
use Michal78\Tasks\Support\TaskRunner;

class Tasks
{
    public function __construct(
        protected TaskRunner $taskRunner,
    ) {
    }

    public function getDueTasks(): Collection
    {
        return Task::query()
            ->due()
            ->orderBy('run_at')
            ->get();
    }

    public function runDueTasks(): int
    {
        $processed = 0;

        foreach ($this->getDueTasks() as $task) {
            $this->taskRunner->run($task);
            $processed++;
        }

        return $processed;
    }
}
