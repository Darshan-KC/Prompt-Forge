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
        Schema::create('prompt_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prompt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ai_model_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('version_number')->nullable();
            $table->string('status')->default('running');
            $table->unsignedBigInteger('tokens_in')->default(0);
            $table->unsignedBigInteger('tokens_out')->default(0);
            $table->unsignedBigInteger('tokens')->default(0);
            $table->unsignedInteger('latency_ms')->default(0);
            $table->decimal('cost', 12, 6)->default(0);
            $table->longText('output')->nullable();
            $table->string('error_code')->nullable();
            $table->json('variables')->nullable();
            $table->timestamps();

            $table->index(['prompt_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompt_runs');
    }
};
