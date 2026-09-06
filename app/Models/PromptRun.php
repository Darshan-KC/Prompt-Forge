<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'prompt_id',
    'user_id',
    'provider_id',
    'ai_model_id',
    'version_number',
    'status',
    'tokens_in',
    'tokens_out',
    'tokens',
    'latency_ms',
    'cost',
    'output',
    'error_code',
    'variables',
])]
class PromptRun extends Model
{
    /** @use HasFactory<\Database\Factories\PromptRunFactory> */
    use HasFactory;

    protected $casts = [
        'status' => 'string',
        'tokens_in' => 'integer',
        'tokens_out' => 'integer',
        'tokens' => 'integer',
        'latency_ms' => 'integer',
        'cost' => 'float',
        'variables' => 'array',
    ];

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }
}
