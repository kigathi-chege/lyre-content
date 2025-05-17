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
        if (!Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                basic_fields($table, 'menu_items');
                $table->string('name')->nullable();
                $table->tinyInteger('order')->default(0);
                $table->foreignId('menu_id')->nullable()->constrained('menus')->cascadeOnDelete();
                $table->foreignId('page_id')->nullable()->constrained('pages')->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
                $table->boolean('is_external')->default(false);
                $table->foreignId('icon_id')->nullable()->constrained('icons');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
