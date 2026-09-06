<?php

namespace Database\Factories;

use App\Models\Prompt;
use App\Models\PromptVariable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PromptVariable>
 */
class PromptVariableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = fake()->unique()->word();

        return [
            'prompt_id' => Prompt::factory(),
            'key' => $key,
            'label' => Str::headline($key),
            'default_value' => fake()->sentence(),
            'sort_order' => 0,
        ];
    }
}
