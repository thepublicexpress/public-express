<?php
// database/migrations/2026_06_27_000010_add_followers_to_reporter_monetisation.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reporter_monetisation', function (Blueprint $table) {
            $table->integer('total_followers')->default(0)->after('total_points');
            $table->integer('required_followers_met')->default(false)->after('total_followers');
            $table->integer('required_views_met')->default(false)->after('required_followers_met');
            $table->boolean('all_criteria_met')->default(false)->after('required_views_met');
        });
    }

    public function down(): void
    {
        Schema::table('reporter_monetisation', function (Blueprint $table) {
            $table->dropColumn([
                'total_followers',
                'required_followers_met',
                'required_views_met',
                'all_criteria_met'
            ]);
        });
    }
};