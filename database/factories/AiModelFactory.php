<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiModel>
 */
class AiModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id' => Provider::factory(),
            'slug' => fake()->unique()->slug(2),
            'name' => fake()->words(2, true),
            'context' => fake()->numberBetween(8000, 1000000),
            'input_price' => fake()->randomFloat(6, 0, 2),
            'output_price' => fake()->randomFloat(6, 0, 10),
            'supports_vision' => fake()->boolean(),
            'supports_streaming' => fake()->boolean(),
            'supports_json' => fake()->boolean(),
        ];
    }
}
