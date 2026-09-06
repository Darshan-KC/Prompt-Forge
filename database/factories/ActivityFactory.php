<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Prompt;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['run', 'version', 'favorite', 'project']);

        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'prompt_id' => Prompt::factory(),
            'type' => $type,
            'text' => fake()->sentence(),
            'meta' => null,
        ];
    }
}
