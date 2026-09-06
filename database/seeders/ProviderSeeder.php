<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\Provider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the AI provider and model catalog.
     */
    public function run(): void
    {
        $providers = require resource_path('data/providers.php');

        foreach ($providers as $provider) {
            $models = $provider['models'];
            unset($provider['models'], $provider['id']);

            $providerRow = Provider::updateOrCreate(
                ['slug' => $provider['slug']],
                $provider
            );

            foreach ($models as $model) {
                AiModel::updateOrCreate(
                    ['provider_id' => $providerRow->id, 'slug' => $model['slug']],
                    [
                        'name' => $model['name'],
                        'context' => $model['context'],
                        'input_price' => $model['pricing']['input'],
                        'output_price' => $model['pricing']['output'],
                        'supports_vision' => $model['supports']['vision'],
                        'supports_streaming' => $model['supports']['streaming'],
                        'supports_json' => $model['supports']['json'],
                    ]
                );
            }
        }
    }
}
