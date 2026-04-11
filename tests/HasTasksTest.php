<?php

namespace Michal78\Tasks\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Michal78\Tasks\Models\Task;
use Michal78\Tasks\Models\TaskLog;
use Michal78\Tasks\Tests\Fixtures\SampleAction;
use Michal78\Tasks\Tests\Fixtures\SampleEvent;
use Michal78\Tasks\Tests\Fixtures\SampleService;

class HasTasksTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        SampleAction::$calls = [];
        SampleService::$calls = [];
    }

    public function test_model_can_schedule_tasks(): void
    {
        $user = User::create(['name' => 'Test User']);

        $task = $user->scheduleActionTask(
            name: 'Action Task',
            actionClass: SampleAction::class,
            runAt: now()->addMinute(),
        );

        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame($user->id, $task->taskable_id);
        $this->assertSame(User::class, $task->taskable_type);
        $this->assertSame(Task::TYPE_ACTION, $task->type);
        $this->assertCount(1, $user->tasks);
    }

    public function test_due_command_task_passes_model_to_command(): void
    {
        $user = User::create(['name' => 'Command User']);

        $user->scheduleCommandTask(
            name: 'Command Task',
            command: 'tasks:test-command',
            runAt: now()->subMinute(),
            payload: ['--flag' => 'ok'],
        );

        Artisan::call('tasks:run-due');

        $this->assertSame(User::class, app('task_test.last_command_model_type'));
        $this->assertSame((string) $user->id, app('task_test.last_command_model_id'));
        $this->assertSame('ok', app('task_test.last_command_flag'));
    }

    public function test_due_action_task_receives_model_payload_and_task(): void
    {
        $user = User::create(['name' => 'Action User']);

        $task = $user->scheduleActionTask(
            name: 'Action Task',
            actionClass: SampleAction::class,
            runAt: now()->subMinute(),
            payload: ['token' => 'abc'],
        );

        Artisan::call('tasks:run-due');

        $this->assertCount(1, SampleAction::$calls);
        $this->assertSame($user->id, SampleAction::$calls[0]['model_id']);
        $this->assertSame('abc', SampleAction::$calls[0]['payload']['token']);
        $this->assertSame($task->id, SampleAction::$calls[0]['task_id']);
        $this->assertSame(Task::STATUS_SUCCEEDED, $task->fresh()->status);
    }

    public function test_due_event_task_dispatches_event_with_model(): void
    {
        Event::fake([SampleEvent::class]);
        $user = User::create(['name' => 'Event User']);

        $task = $user->scheduleEventTask(
            name: 'Event Task',
            eventClass: SampleEvent::class,
            runAt: now()->subMinute(),
            payload: ['source' => 'test'],
        );

        Artisan::call('tasks:run-due');

        Event::assertDispatched(SampleEvent::class, function (SampleEvent $event) use ($task, $user) {
            return $event->model->is($user)
                && $event->task->is($task)
                && $event->payload['source'] === 'test';
        });
    }

    public function test_due_service_task_calls_configured_method(): void
    {
        $user = User::create(['name' => 'Service User']);

        $user->scheduleServiceTask(
            name: 'Service Task',
            serviceClass: SampleService::class,
            runAt: now()->subMinute(),
            payload: ['service' => true],
            method: 'run',
        );

        Artisan::call('tasks:run-due');

        $this->assertCount(1, SampleService::$calls);
        $this->assertSame($user->id, SampleService::$calls[0]['model_id']);
        $this->assertTrue(SampleService::$calls[0]['payload']['service']);
    }

    public function test_it_logs_task_runs_when_enabled(): void
    {
        config()->set('laravel-tasks.logging.enabled', true);
        $user = User::create(['name' => 'Log User']);

        $task = $user->scheduleServiceTask(
            name: 'Log Task',
            serviceClass: SampleService::class,
            runAt: now()->subMinute(),
        );

        Artisan::call('tasks:run-due');

        $log = TaskLog::query()->where('task_id', $task->id)->first();

        $this->assertNotNull($log);
        $this->assertSame(Task::STATUS_SUCCEEDED, $log->status);
        $this->assertNotNull($log->started_at);
        $this->assertNotNull($log->finished_at);
    }

    public function test_it_skips_task_logs_when_disabled(): void
    {
        config()->set('laravel-tasks.logging.enabled', false);
        $user = User::create(['name' => 'No Log User']);

        $user->scheduleServiceTask(
            name: 'No Log Task',
            serviceClass: SampleService::class,
            runAt: now()->subMinute(),
        );

        Artisan::call('tasks:run-due');

        $this->assertDatabaseCount('task_logs', 0);
    }
}
