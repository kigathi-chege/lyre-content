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
        if (!Schema::hasTable('buttons')) {
            Schema::create('buttons', function (Blueprint $table) {
                basic_fields($table, 'buttons');
                $table->string('name')->unique();
                $table->string('text')->nullable();
                $table->json('misc')->nullable();
                $table->foreignId('icon_id')->nullable()->constrained('icons');
            });
        }

        if (!Schema::hasColumn('buttons', "misc")) {
            Schema::table('buttons', function (Blueprint $table) {
                $table->json('misc')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buttons');
    }
};
