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
        $tableName = $prefix . 'page_sections';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName, $prefix) {
                $table->id();
                $table->timestamps();
                $table->tinyInteger('order')->default(0);
                $table->foreignId('page_id')->constrained($prefix . 'pages')->cascadeOnDelete();
                $table->foreignId('section_id')->constrained($prefix . 'sections')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'page_sections';

        Schema::dropIfExists($tableName);
    }
};
