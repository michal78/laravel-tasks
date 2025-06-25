<?php

namespace Michal78\Tasks\Console\Commands;

use Illuminate\Console\Command;
use Michal78\Tasks\Models\Task;
use Illuminate\Support\Facades\Bus;

class DispatchDueTasksCommand extends Command
{
    protected $signature = 'tasks:dispatch';
    protected $description = 'Dispatch all due tasks';

    public function handle(): int
    {
        $tasks = Task::where('status', Task::STATUS_PENDING)
            ->whereNotNull('job')
            ->whereNotNull('due_date')
            ->where('due_date', '<=', now())
            ->get();

        foreach ($tasks as $task) {
            $job = new $task->job($task->job_data);
            Bus::dispatch($job);

            $task->status = Task::STATUS_RUNNING;
            $task->save();
        }

        $this->info($tasks->count() . ' tasks dispatched.');

        return Command::SUCCESS;
    }
}
