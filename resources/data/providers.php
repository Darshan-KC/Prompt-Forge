<?php

/*
|--------------------------------------------------------------------------
| Mock provider & model catalog
|--------------------------------------------------------------------------
| Static provider/model data used to render provider pickers, model badges,
| analytics breakdowns and playground controls.
|
| TODO: Replace with real AI provider catalog (Provider/Model Eloquent models
| driven by an AI engineer backend).
*/

return [
    [
        'id' => 1,
        'slug' => 'openai',
        'name' => 'OpenAI',
        'tagline' => 'GPT series',
        'color' => '#10a37f',
        'badge' => 'SK',
        'status' => 'connected',
        'models' => [
            ['slug' => 'gpt-4o', 'name' => 'GPT-4o', 'context' => 128000, 'pricing' => ['input' => 2.5, 'output' => 10.0], 'supports' => ['vision' => true, 'streaming' => true, 'json' => true]],
            ['slug' => 'gpt-4o-mini', 'name' => 'GPT-4o mini', 'context' => 128000, 'pricing' => ['input' => 0.15, 'output' => 0.6], 'supports' => ['vision' => true, 'streaming' => true, 'json' => true]],
            ['slug' => 'o3-mini', 'name' => 'o3-mini', 'context' => 200000, 'pricing' => ['input' => 1.1, 'output' => 4.4], 'supports' => ['vision' => false, 'streaming' => true, 'json' => true]],
            ['slug' => 'o1', 'name' => 'o1', 'context' => 200000, 'pricing' => ['input' => 15.0, 'output' => 60.0], 'supports' => ['vision' => true, 'streaming' => false, 'json' => false]],
        ],
    ],
    [
        'id' => 2,
        'slug' => 'anthropic',
        'name' => 'Anthropic',
        'tagline' => 'Claude series',
        'color' => '#d97757',
        'badge' => 'AI',
        'status' => 'connected',
        'models' => [
            ['slug' => 'claude-4-opus', 'name' => 'Claude Opus 4', 'context' => 200000, 'pricing' => ['input' => 15.0, 'output' => 75.0], 'supports' => ['vision' => true, 'streaming' => true, 'json' => true]],
            ['slug' => 'claude-3-7-sonnet', 'name' => 'Claude Sonnet 3.7', 'context' => 200000, 'pricing' => ['input' => 3.0, 'output' => 15.0], 'supports' => ['vision' => true, 'streaming' => true, 'json' => true]],
            ['slug' => 'claude-3-5-haiku', 'name' => 'Claude Haiku 3.5', 'context' => 200000, 'pricing' => ['input' => 0.8, 'output' => 4.0], 'supports' => ['vision' => true, 'streaming' => true, 'json' => true]],
        ],
    ],
    [
        'id' => 3,
        'slug' => 'google',
        'name' => 'Google Gemini',
        'tagline' => 'Gemini series',
        'color' => '#4285f4',
        'badge' => 'G',
        'status' => 'connected',
        'models' => [
            ['slug' => 'gemini-2.5-pro', 'name' => 'Gemini 2.5 Pro', 'context' => 1000000, 'pricing' => ['input' => 1.25, 'output' => 10.0], 'supports' => ['vision' => true, 'streaming' => true, 'json' => true]],
            ['slug' => 'gemini-2.5-flash', 'name' => 'Gemini 2.5 Flash', 'context' => 1000000, 'pricing' => ['input' => 0.3, 'output' => 2.5], 'supports' => ['vision' => true, 'streaming' => true, 'json' => true]],
        ],
    ],
    [
        'id' => 4,
        'slug' => 'openrouter',
        'name' => 'OpenRouter',
        'tagline' => 'Unified access',
        'color' => '#7d5cff',
        'badge' => 'OR',
        'status' => 'connected',
        'models' => [
            ['slug' => 'meta-llama-4-maverick', 'name' => 'Llama 4 Maverick', 'context' => 1000000, 'pricing' => ['input' => 0.2, 'output' => 0.85], 'supports' => ['vision' => true, 'streaming' => true, 'json' => true]],
            ['slug' => 'deepseek-chat', 'name' => 'DeepSeek V3', 'context' => 128000, 'pricing' => ['input' => 0.3, 'output' => 0.9], 'supports' => ['vision' => false, 'streaming' => true, 'json' => true]],
        ],
    ],
    [
        'id' => 5,
        'slug' => 'custom',
        'name' => 'Custom Provider',
        'tagline' => 'Self-hosted / local',
        'color' => '#71717a',
        'badge' => 'LX',
        'status' => 'configured',
        'models' => [
            ['slug' => 'local-qwen-32b', 'name' => 'Qwen 32B (local)', 'context' => 32768, 'pricing' => ['input' => 0.0, 'output' => 0.0], 'supports' => ['vision' => false, 'streaming' => true, 'json' => false]],
            ['slug' => 'local-llama-70b', 'name' => 'Llama 3.1 70B (local)', 'context' => 131072, 'pricing' => ['input' => 0.0, 'output' => 0.0], 'supports' => ['vision' => false, 'streaming' => true, 'json' => false]],
        ],
    ],
];