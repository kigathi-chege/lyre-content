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
        $tableName = $prefix . 'data';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName, $prefix) {
                $table->id();
                $table->timestamps();
                $table->morphs('type');
                $table->json('filters');

                $table->foreignId('section_id')->constrained($prefix . 'sections')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) {
                if (Schema::hasColumn('data', 'type_type') && Schema::hasColumn('data', 'type_id')) {
                    $table->dropMorphs('type');
                }
            });

            if (!Schema::hasColumn($tableName, 'type')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('type');
                });
            }

            if (!Schema::hasColumn($tableName, 'name')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('name');
                });
            }

            if (!Schema::hasColumn($tableName, 'order')) {
                Schema::table($tableName, function (Blueprint $table) {
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
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'data';

        Schema::dropIfExists($tableName);
    }
};
