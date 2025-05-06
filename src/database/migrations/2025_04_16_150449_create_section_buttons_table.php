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
        if (!Schema::hasTable('section_buttons')) {
            Schema::create('section_buttons', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->tinyInteger('order')->default(0);
                $table->foreignId('section_id')->constrained()->cascadeOnDelete();
                $table->foreignId('button_id')->constrained()->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_buttons');
    }
};
