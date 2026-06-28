<?php
// database/migrations/2026_06_27_000001_update_revenue_settings_for_monetisation.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revenue_settings', function (Blueprint $table) {
            // Monetisation Control
            $table->boolean('is_monetisation_active')->default(false)->after('points_per_news');
            $table->boolean('is_wallet_visible')->default(false)->after('is_monetisation_active');
            
            // Minimum Requirements
            $table->integer('min_points_for_monetisation')->default(100)->after('is_wallet_visible');
            $table->integer('min_views_for_monetisation')->default(1000)->after('min_points_for_monetisation');
            
            // Fake Views Detection
            $table->integer('max_views_per_ip_per_day')->default(5)->after('min_views_for_monetisation');
            
            // Point to Rupee Conversion
            $table->decimal('point_to_rupee_rate', 10, 2)->default(0.10)->after('max_views_per_ip_per_day');
            
            // Terms & Conditions
            $table->text('monetisation_terms')->nullable()->after('point_to_rupee_rate');
        });

        // Update existing settings
        DB::table('revenue_settings')->update([
            'is_monetisation_active' => false,
            'is_wallet_visible' => false,
            'min_points_for_monetisation' => 100,
            'min_views_for_monetisation' => 1000,
            'max_views_per_ip_per_day' => 5,
            'point_to_rupee_rate' => 0.10,
        ]);
    }

    public function down(): void
    {
        Schema::table('revenue_settings', function (Blueprint $table) {
            $table->dropColumn([
                'is_monetisation_active',
                'is_wallet_visible',
                'min_points_for_monetisation',
                'min_views_for_monetisation',
                'max_views_per_ip_per_day',
                'point_to_rupee_rate',
                'monetisation_terms'
            ]);
        });
    }
};