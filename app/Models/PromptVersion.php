<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['prompt_id', 'author_id', 'number', 'note', 'payload'])]
class PromptVersion extends Model
{
    /** @use HasFactory<\Database\Factories\PromptVersionFactory> */
    use HasFactory;

    protected $casts = [
        'payload' => 'array',
        'number' => 'integer',
    ];

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
