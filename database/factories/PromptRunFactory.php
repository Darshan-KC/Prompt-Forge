<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Models\Prompt;
use App\Models\PromptRun;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromptRun>
 */
class PromptRunFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['success', 'success', 'success', 'error', 'cancelled']);

        return [
            'prompt_id' => Prompt::factory(),
            'user_id' => User::factory(),
            'provider_id' => Provider::factory(),
            'ai_model_id' => AiModel::factory(),
            'version_number' => fake()->numberBetween(1, 10),
            'status' => $status,
            'tokens_in' => fake()->numberBetween(100, 10000),
            'tokens_out' => $status === 'success' ? fake()->numberBetween(50, 3000) : 0,
            'tokens' => fn (array $attributes) => $attributes['tokens_in'] + $attributes['tokens_out'],
            'latency_ms' => $status === 'cancelled' ? 0 : fake()->numberBetween(200, 5000),
            'cost' => fake()->randomFloat(6, 0, 0.1),
            'output' => $status === 'success' ? fake()->paragraphs(3, true) : null,
            'error_code' => $status === 'error' ? fake()->randomElement(['provider.error.rate_limit', 'provider.error.timeout']) : null,
            'variables' => [],
        ];
    }
}
