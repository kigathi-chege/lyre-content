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
        if (!Schema::hasTable('data')) {
            Schema::create('data', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->morphs('type');
                $table->json('filters');

                $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('data')) {
            Schema::table('data', function (Blueprint $table) {
                if (Schema::hasColumn('data', 'type_type') && Schema::hasColumn('data', 'type_id')) {
                    $table->dropMorphs('type');
                }
            });

            if (!Schema::hasColumn('data', 'type')) {
                Schema::table('data', function (Blueprint $table) {
                    $table->string('type');
                });
            }

            if (!Schema::hasColumn('data', 'name')) {
                Schema::table('data', function (Blueprint $table) {
                    $table->string('name');
                });
            }

            if (!Schema::hasColumn('data', 'order')) {
                Schema::table('data', function (Blueprint $table) {
                    $table->tinyInteger('order')->default(0);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data');
    }
};
