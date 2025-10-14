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
        $tableName = $prefix . 'texts';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName, $prefix) {
                basic_fields($table, $tableName);
                $table->string('name')->unique();
                $table->text('content')->nullable();
                $table->foreignId('icon_id')->nullable()->constrained($prefix . 'icons');
            });
        }

        if (!Schema::hasColumn($tableName, "misc")) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->json('misc')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'texts';

        Schema::dropIfExists($tableName);
    }
};
