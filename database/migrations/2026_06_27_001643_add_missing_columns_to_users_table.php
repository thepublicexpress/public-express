<?php
// database/migrations/2026_06_27_000006_add_columns_to_news_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            // Check if columns don't exist
            if (!Schema::hasColumn('news', 'block_id')) {
                $table->foreignId('block_id')->nullable()->constrained()->onDelete('set null')->after('tehsil_id');
            }
            
            if (!Schema::hasColumn('news', 'is_national')) {
                $table->boolean('is_national')->default(false)->after('is_featured');
            }
            
            if (!Schema::hasColumn('news', 'is_state')) {
                $table->boolean('is_state')->default(false)->after('is_national');
            }
            
            if (!Schema::hasColumn('news', 'approved_by_level')) {
                $table->string('approved_by_level')->nullable()->after('approved_by');
            }
            
            if (!Schema::hasColumn('news', 'location_required')) {
                $table->boolean('location_required')->default(true)->after('is_state');
            }
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $columns = ['block_id', 'is_national', 'is_state', 'approved_by_level', 'location_required'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('news', $column)) {
                    if ($column === 'block_id') {
                        $table->dropForeign(['block_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};