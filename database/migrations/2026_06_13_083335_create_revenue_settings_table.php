<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('revenue_settings')) {
            Schema::create('revenue_settings', function (Blueprint $table) {
                $table->id();
                $table->integer('views_per_point')->default(100);
                $table->decimal('point_value', 10, 2)->default(0.10);
                $table->decimal('min_withdrawal', 10, 2)->default(100);
                $table->decimal('max_withdrawal', 10, 2)->default(10000);
                $table->integer('points_per_news')->default(10);
                $table->timestamps();
            });

            // Insert default settings
            DB::table('revenue_settings')->insert([
                'views_per_point' => 100,
                'point_value' => 0.10,
                'min_withdrawal' => 100,
                'max_withdrawal' => 10000,
                'points_per_news' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_settings');
    }
};