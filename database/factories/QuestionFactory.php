<?php

namespace Database\Factories;

use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(QuestionType::cases())->value,
            'question' => fake()->sentence() . '?',
            'mark' => fake()->numberBetween(1, 10),
            'published' => fake()->boolean(80),
        ];
    }
}
