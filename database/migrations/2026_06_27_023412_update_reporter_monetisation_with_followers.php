<?php
// database/migrations/2026_06_27_000015_update_reporter_monetisation_with_followers.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reporter_monetisation', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('reporter_monetisation', 'total_followers')) {
                $table->integer('total_followers')->default(0)->after('total_points');
            }
            
            if (!Schema::hasColumn('reporter_monetisation', 'required_followers_met')) {
                $table->boolean('required_followers_met')->default(false)->after('total_followers');
            }
            
            if (!Schema::hasColumn('reporter_monetisation', 'required_views_met')) {
                $table->boolean('required_views_met')->default(false)->after('required_followers_met');
            }
            
            if (!Schema::hasColumn('reporter_monetisation', 'all_criteria_met')) {
                $table->boolean('all_criteria_met')->default(false)->after('required_views_met');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reporter_monetisation', function (Blueprint $table) {
            $columns = ['total_followers', 'required_followers_met', 'required_views_met', 'all_criteria_met'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('reporter_monetisation', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};