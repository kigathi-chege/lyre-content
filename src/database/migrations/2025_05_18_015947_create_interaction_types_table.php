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
        if (!Schema::hasTable('interaction_types')) {
            Schema::create('interaction_types', function (Blueprint $table) {
                basic_fields($table, 'interaction_types');
                $table->string('name')->unique();
                $table->foreignId('antonym_id')->nullable()->constrained('interaction_types')->onDelete('set null');
                $table->foreignId('icon_id')->nullable()->constrained('icons')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('interaction_types', 'status')) {
            Schema::table('interaction_types', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('active')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interaction_types');
    }
};
