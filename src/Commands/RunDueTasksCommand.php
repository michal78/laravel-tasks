<?php

namespace Michal78\Tasks\Commands;

use Illuminate\Console\Command;
use Michal78\Tasks\Tasks;

class RunDueTasksCommand extends Command
{
    protected $signature = 'tasks:run-due';

    protected $description = 'Run all due model tasks';

    public function handle(Tasks $tasks): int
    {
        $processed = $tasks->runDueTasks();

        $this->info(sprintf('Processed %d due task(s).', $processed));

        return self::SUCCESS;
    }
}
