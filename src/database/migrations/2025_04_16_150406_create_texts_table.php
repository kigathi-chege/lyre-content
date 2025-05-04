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
        if (!Schema::hasTable('texts')) {
            Schema::create('texts', function (Blueprint $table) {
                basic_fields($table, 'texts');
                $table->string('name')->unique();
                $table->text('content')->nullable();
                $table->foreignId('icon_id')->nullable()->constrained('icons');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('texts');
    }
};
