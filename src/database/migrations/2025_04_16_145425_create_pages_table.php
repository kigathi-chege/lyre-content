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
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                basic_fields($table, 'pages');
                $table->string('title')->unique();
                $table->text('content')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_published')->default(false);
                $table->boolean('is_external')->default(false);
                $table->string('external_link')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('keywords')->nullable();
                $table->string('canonical_url')->nullable();
                $table->string('og_title')->nullable();
                $table->text('og_description')->nullable();
                $table->string('og_image')->nullable();
                $table->string('twitter_title')->nullable();
                $table->text('twitter_description')->nullable();
                $table->string('twitter_image')->nullable();
                $table->json('schema_markup')->nullable();
                $table->string('robots_meta_tag')->default('index');
                $table->integer('total_views')->default(0);
                $table->foreignId('icon_id')->nullable()->constrained('icons');
            });
        }

        if (!Schema::hasColumn('pages', 'parent_id')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->foreignId('parent_id')->nullable()->constrained('pages')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
