<?php

namespace Michal78\Tasks\Tests;

use Michal78\Tasks\Models\Task;

class HasTasksTest extends TestCase
{
    public function test_model_can_create_and_retrieve_tasks()
    {
        $user = User::create(['name' => 'Test User']);

        $task = $user->addTask(['name' => 'Test Task']);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($user->id, $task->owner_id);
        $this->assertEquals(User::class, $task->owner_class);

        $this->assertCount(1, $user->tasks);
        $this->assertTrue($user->tasks->first()->is($task));
        $this->assertCount(1, $user->pendingTasks());
    }
}
