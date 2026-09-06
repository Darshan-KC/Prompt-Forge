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
        Schema::table('prompts', function (Blueprint $table) {
            $table->foreignId('folder_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->after('folder_id')->constrained()->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->after('project_id')->constrained()->nullOnDelete();
            $table->foreignId('ai_model_id')->nullable()->after('provider_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['ai_model_id']);

            $table->dropColumn(['folder_id', 'project_id', 'provider_id', 'ai_model_id']);
        });
    }
};
