<?php

namespace Database\Factories;

use App\Models\Folder;
use App\Models\Project;
use App\Models\Prompt;
use App\Models\Provider;
use App\Models\AiModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Prompt>
 */
class PromptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'user_id' => User::factory(),
            'folder_id' => Folder::factory(),
            'project_id' => Project::factory(),
            'provider_id' => Provider::factory(),
            'ai_model_id' => AiModel::factory(),
            'slug' => Str::slug($name),
            'name' => $name,
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['Development', 'Writing', 'Research', 'Marketing', 'Education', 'Data Analysis', 'Productivity']),
            'system_prompt' => fake()->paragraph(),
            'template' => fake()->paragraph(),
            'temperature' => fake()->randomFloat(1, 0, 1),
            'top_p' => 1,
            'max_tokens' => fake()->randomElement([512, 1024, 2048, 4096]),
            'favorite' => fake()->boolean(20),
            'status' => fake()->randomElement(['draft', 'published']),
            'current_version' => 1,
            'usage_count' => fake()->numberBetween(0, 1000),
            'last_run_at' => fake()->dateTime(),
        ];
    }
}
