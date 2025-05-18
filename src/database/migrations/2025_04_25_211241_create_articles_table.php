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
        if (!Schema::hasTable('articles')) {
            Schema::create('articles', function (Blueprint $table) {
                basic_fields($table, 'articles');
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->text('content');
                $table->integer("views")->default(0);
                $table->boolean('is_featured')->default(false);
                $table->boolean('unpublished')->default(false);
                $table->dateTime('published_at')->nullable();
                $table->dateTime('sent_as_newsletter_at')->nullable();
                $table->foreignId("author_id")->nullable()->constrained("users");

                // NOTE: Kigathi - May 18 2025 - Potential article statuses:
                // $table->enum('status', ['draft', 'published', 'archived', 'deleted'])->default('draft')->nullable();
                // This will mean that published_at becomes an automatically updated field
                // We will also need a more complex scheduling system for future publications
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
