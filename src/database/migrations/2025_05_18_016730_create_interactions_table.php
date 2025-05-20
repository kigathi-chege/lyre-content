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
        if (!Schema::hasTable('interactions')) {
            Schema::create('interactions', function (Blueprint $table) {
                basic_fields($table, 'interactions');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('interaction_type_id')->nullable()->constrained('interaction_types')->nullOnDelete();
                $table->text('content')->nullable();
                $table->morphs('entity');
            });
        }

        if (!Schema::hasColumn('interactions', 'interaction_type_id')) {
            Schema::table('interactions', function (Blueprint $table) {
                $table->foreignId('interaction_type_id')->nullable()->constrained('interaction_types')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('interactions', 'status')) {
            Schema::table('interactions', function (Blueprint $table) {
                $table->enum('status', ['published', 'deleted'])->default('published')->nullable();
            });
        }

        if (Schema::hasColumn('interactions', 'type')) {
            Schema::table('interactions', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
