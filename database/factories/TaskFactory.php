<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Michal78\Tasks\Models\Task;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'taskable_id' => 1,
            'taskable_type' => 'App\\Models\\User',
            'type' => fake()->randomElement([
                Task::TYPE_ACTION,
                Task::TYPE_COMMAND,
                Task::TYPE_EVENT,
                Task::TYPE_SERVICE,
            ]),
            'target' => 'app:task',
            'method' => null,
            'payload' => ['example' => true],
            'run_at' => now()->addMinute(),
            'status' => Task::STATUS_PENDING,
            'error_message' => null,
            'last_ran_at' => null,
        ];
    }
}
