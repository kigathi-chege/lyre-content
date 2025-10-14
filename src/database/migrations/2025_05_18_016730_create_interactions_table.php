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
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'interactions';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName, $prefix) {
                basic_fields($table, $tableName);
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('interaction_type_id')->nullable()->constrained($prefix . 'interaction_types')->nullOnDelete();
                $table->text('content')->nullable();
                $table->morphs('entity');
            });
        }

        if (!Schema::hasColumn($tableName, 'interaction_type_id')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('interaction_type_id')->nullable()->constrained('interaction_types')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn($tableName, 'status')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('status', ['published', 'deleted'])->default('published')->nullable();
            });
        }

        if (Schema::hasColumn($tableName, 'type')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'interactions';

        Schema::dropIfExists($tableName);
    }
};
