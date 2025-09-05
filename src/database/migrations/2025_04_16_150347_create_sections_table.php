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
        if (!Schema::hasTable('sections')) {
            Schema::create('sections', function (Blueprint $table) {
                basic_fields($table, 'sections');
                $table->text('name')->comment("This refers to the name of the frontend component");
                $table->text('title')->nullable();
                $table->text('subtitle')->nullable();
                $table->json('misc')->nullable();
                $table->foreignId('icon_id')->nullable()->constrained('icons');
            });
        }

        if (!Schema::hasColumn('sections', 'icon_id')) {
            Schema::table('sections', function (Blueprint $table) {
                $table->foreignId('icon_id')->nullable()->constrained('icons');
            });
        }

        if (Schema::hasColumn("sections", "title")) {
            $columnType = Schema::getColumnType('sections', 'title');
            if ($columnType == 'varchar') {
                Schema::table('sections', function (Blueprint $table) {
                    $table->text('title')->nullable()->change();
                });
            }
        }

        if (Schema::hasColumn("sections", "subtitle")) {
            $columnType = Schema::getColumnType('sections', 'subtitle');
            if ($columnType == 'varchar') {
                Schema::table('sections', function (Blueprint $table) {
                    $table->text('subtitle')->nullable()->change();
                });
            }
        }

        if (!Schema::hasColumn("sections", "component")) {
            Schema::table('sections', function (Blueprint $table) {
                $table->string('component')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
