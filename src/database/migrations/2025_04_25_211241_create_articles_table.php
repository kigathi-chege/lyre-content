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
