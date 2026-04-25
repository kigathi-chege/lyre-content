<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'articles';
        $userTable = Schema::hasTable('users') ? 'users' : 'user';

        if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'published_by')) {
            Schema::table($tableName, function (Blueprint $table) use ($userTable) {
                $table->foreignId('published_by')->nullable()->after('published_at')->constrained($userTable)->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $prefix = config('lyre.table_prefix');
        $tableName = $prefix . 'articles';

        if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'published_by')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['published_by']);
                $table->dropColumn('published_by');
            });
        }
    }
};
