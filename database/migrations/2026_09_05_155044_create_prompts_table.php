<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->longText('system_prompt')->nullable();
            $table->longText('template');
            $table->decimal('temperature', 4, 2)->default(0.7);
            $table->decimal('top_p', 4, 2)->default(1);
            $table->unsignedInteger('max_tokens')->default(1024);
            $table->boolean('favorite')->default(false);
            $table->string('status')->default('draft');
            $table->unsignedInteger('current_version')->nullable();
            $table->unsignedBigInteger('usage_count')->default(0);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'category']);
            $table->unique(['user_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};
