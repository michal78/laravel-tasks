<?php

namespace Michal78\Tasks\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Michal78\Tasks\Models\Task;
use Michal78\Tasks\Tests\Jobs\DummyJob;

class DispatchDueTasksCommandTest extends TestCase
{
    public function test_command_dispatches_due_tasks()
    {
        Queue::fake();

        $user = User::create(['name' => 'Test']);

        $dueTask = $user->addTask([
            'name' => 'Due',
            'job' => DummyJob::class,
            'job_data' => json_encode(['foo' => 'bar']),
            'due_date' => now()->subMinute(),
        ]);

        $futureTask = $user->addTask([
            'name' => 'Future',
            'job' => DummyJob::class,
            'due_date' => now()->addHour(),
        ]);

        Artisan::call('tasks:dispatch');

        Queue::assertPushed(DummyJob::class, 1);
        $this->assertEquals(Task::STATUS_RUNNING, $dueTask->fresh()->status);
        $this->assertEquals(Task::STATUS_PENDING, $futureTask->fresh()->status);
    }
}
