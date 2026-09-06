<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Provider>
 */
class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'name' => fake()->company(),
            'tagline' => fake()->catchPhrase(),
            'color' => fake()->hexColor(),
            'badge' => strtoupper(fake()->lexify('??')),
            'status' => 'configured',
        ];
    }
}
