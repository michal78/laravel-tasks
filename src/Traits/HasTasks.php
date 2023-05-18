<?php

namespace Michal78\Tasks\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Michal78\Tasks\Models\Task;

trait HasTasks
{
    // Add task
    public function addTask($task)
    {
        $this->tasks()->create($task);
    }

    // Get pending tasks
    public function pendingTasks()
    {
        // Use scopes from Task model
        return $this->tasks()->pending()->get();
    }

    // Get completed tasks
    public function completedTasks()
    {
        // Use scopes from Task model
        return $this->tasks()->completed()->get();
    }

    // Get all tasks
    public function tasks(): MorphMany
    {
        return $this->morphMany('Michal78\Tasks\Models\Task', 'owner', 'owner_class');
    }

    // Complete task
    public function completeTask(Task $task)
    {
        $task->complete($this);
    }
}
