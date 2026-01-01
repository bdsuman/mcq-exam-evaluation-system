<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            // Use a stable placeholder image to avoid broken remote faker URLs
            'user_id' => \App\Models\User::factory(),
            'avatar' => 'https://placehold.co/100x100?text=Task',
            'is_completed' => $this->faker->randomElement([true, false]),
        ];
    }
}
