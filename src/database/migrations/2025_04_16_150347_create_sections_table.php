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
        $tableName = $prefix . 'sections';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName, $prefix) {
                basic_fields($table, $tableName);
                $table->text('name')->comment("This refers to the name of the frontend component");
                $table->text('title')->nullable();
                $table->text('subtitle')->nullable();
                $table->json('misc')->nullable();
                $table->foreignId('icon_id')->nullable()->constrained($prefix . 'icons');
            });
        }

        if (!Schema::hasColumn($tableName, 'icon_id')) {
            Schema::table($tableName, function (Blueprint $table) use ($prefix) {
                $table->foreignId('icon_id')->nullable()->constrained($prefix . 'icons');
            });
        }

        if (Schema::hasColumn($tableName, "title")) {
            $columnType = Schema::getColumnType($tableName, 'title');
            if ($columnType == 'varchar') {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->text('title')->nullable()->change();
                });
            }
        }

        if (Schema::hasColumn($tableName, "subtitle")) {
            $columnType = Schema::getColumnType($tableName, 'subtitle');
            if ($columnType == 'varchar') {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->text('subtitle')->nullable()->change();
                });
            }
        }

        if (!Schema::hasColumn($tableName, "component")) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('component')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'sections';

        Schema::dropIfExists($tableName);
    }
};
