<?php

namespace Database\Factories;

use App\Models\Prompt;
use App\Models\PromptVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromptVersion>
 */
class PromptVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prompt_id' => Prompt::factory(),
            'author_id' => User::factory(),
            'number' => fake()->unique()->numberBetween(1, 20),
            'note' => fake()->sentence(),
            'payload' => [
                'name' => fake()->words(3, true),
                'description' => fake()->sentence(),
                'system' => fake()->paragraph(),
                'template' => fake()->paragraph(),
                'variables' => [],
                'provider' => fake()->slug(1),
                'model' => fake()->slug(2),
                'temperature' => fake()->randomFloat(1, 0, 1),
                'max_tokens' => 1024,
                'top_p' => 1,
            ],
        ];
    }
}
