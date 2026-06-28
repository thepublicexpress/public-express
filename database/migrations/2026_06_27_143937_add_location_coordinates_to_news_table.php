<?php
// database/migrations/2026_06_28_000002_add_location_coordinates_to_news_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (!Schema::hasColumn('news', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('block_id');
            }
            if (!Schema::hasColumn('news', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('news', 'location_radius')) {
                $table->integer('location_radius')->default(5)->after('longitude');
            }
            if (!Schema::hasColumn('news', 'is_hyperlocal')) {
                $table->boolean('is_hyperlocal')->default(false)->after('location_radius');
            }
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'location_radius', 'is_hyperlocal']);
        });
    }
};