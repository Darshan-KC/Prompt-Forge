<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['prompt_id', 'key', 'label', 'default_value', 'sort_order'])]
class PromptVariable extends Model
{
    /** @use HasFactory<\Database\Factories\PromptVariableFactory> */
    use HasFactory;

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }
}
