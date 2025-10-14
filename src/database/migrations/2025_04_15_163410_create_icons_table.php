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
        $tableName = $prefix . 'icons';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName) {
                basic_fields($table, $tableName);
                $table->string('name')->unique();
                $table->boolean('is_svg')->default(false);
                $table->text('content')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'icons';

        Schema::dropIfExists($tableName);
    }
};
