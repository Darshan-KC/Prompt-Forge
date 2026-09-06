<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'provider_id',
    'slug',
    'name',
    'context',
    'input_price',
    'output_price',
    'supports_vision',
    'supports_streaming',
    'supports_json',
])]
class AiModel extends Model
{
    /** @use HasFactory<\Database\Factories\AiModelFactory> */
    use HasFactory;

    protected $casts = [
        'context' => 'integer',
        'input_price' => 'float',
        'output_price' => 'float',
        'supports_vision' => 'boolean',
        'supports_streaming' => 'boolean',
        'supports_json' => 'boolean',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
