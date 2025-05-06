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
        if (!Schema::hasTable('icons')) {
            Schema::create('icons', function (Blueprint $table) {
                basic_fields($table, 'icons');
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
        Schema::dropIfExists('icons');
    }
};
