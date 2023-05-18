<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Michal78\Tasks\Models\Task;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->faker->languageCode('da_DK');
        $this->faker->locale('da_DK');

        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'status' => fake()->randomElement([
                Task::STATUS_PENDING,
                Task::STATUS_COMPLETED,
                Task::STATUS_RUNNING,
            ]),
            'owner_id' => 1,
            'owner_class' => 'App\Models\User',
            'assignee_id' => 1,
            'assignee_class' => 'App\Models\User',
            'due_date' => fake()->dateTimeBetween('-3 month', '+3 month'),
            'priority' => fake()->numberBetween(0, 10),
            'completed_at' => null,
            'completed_by' => null,
            'created_at' => fake()->dateTimeBetween('-3 month', '+3 month'),
            'updated_at' => fake()->dateTimeBetween('-3 month', '+3 month'),
        ];
    }
}
