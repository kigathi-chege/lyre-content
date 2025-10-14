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
        $tableName = $prefix . 'menu_items';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName, $prefix) {
                basic_fields($table, $tableName);
                $table->string('name')->nullable();
                $table->tinyInteger('order')->default(0);
                $table->foreignId('menu_id')->nullable()->constrained($prefix . 'menus')->cascadeOnDelete();
                $table->foreignId('page_id')->nullable()->constrained($prefix . 'pages')->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained($prefix . 'menu_items')->cascadeOnDelete();
                $table->boolean('is_external')->default(false);
                $table->foreignId('icon_id')->nullable()->constrained($prefix . 'icons');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'menu_items';

        Schema::dropIfExists($tableName);
    }
};
