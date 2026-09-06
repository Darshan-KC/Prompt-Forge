<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'folder_id',
    'project_id',
    'provider_id',
    'ai_model_id',
    'slug',
    'name',
    'description',
    'category',
    'system_prompt',
    'template',
    'temperature',
    'top_p',
    'max_tokens',
    'favorite',
    'status',
    'current_version',
    'usage_count',
    'last_run_at',
])]
class Prompt extends Model
{
    /** @use HasFactory<\Database\Factories\PromptFactory> */
    use HasFactory;

    protected $casts = [
        'temperature' => 'float',
        'top_p' => 'float',
        'max_tokens' => 'integer',
        'favorite' => 'boolean',
        'current_version' => 'integer',
        'usage_count' => 'integer',
        'last_run_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AiModel::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function variables(): HasMany
    {
        return $this->hasMany(PromptVariable::class)->orderBy('sort_order');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PromptVersion::class)->orderByDesc('number');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(PromptRun::class)->latest();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
