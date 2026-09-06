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
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->unsignedBigInteger('context')->default(0);
            $table->decimal('input_price', 12, 6)->default(0);
            $table->decimal('output_price', 12, 6)->default(0);
            $table->boolean('supports_vision')->default(false);
            $table->boolean('supports_streaming')->default(false);
            $table->boolean('supports_json')->default(false);
            $table->timestamps();

            $table->unique(['provider_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
