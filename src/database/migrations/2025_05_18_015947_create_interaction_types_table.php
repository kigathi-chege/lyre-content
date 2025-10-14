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
        $tableName = $prefix . 'interaction_types';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName, $prefix) {
                basic_fields($table, $tableName);
                $table->string('name')->unique();
                $table->foreignId('antonym_id')->nullable()->constrained($prefix . 'interaction_types')->onDelete('set null');
                $table->foreignId('icon_id')->nullable()->constrained($prefix . 'icons')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn($tableName, 'status')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('active')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'interaction_types';

        Schema::dropIfExists($tableName);
    }
};
