<?php
// database/migrations/2026_06_27_000003_create_reporter_monetisation_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporter_monetisation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Monetisation Status
            $table->boolean('is_monetisation_active')->default(false);
            
            // Stats
            $table->integer('total_points')->default(0);
            $table->integer('total_views')->default(0);
            $table->integer('unique_views')->default(0);
            $table->integer('fake_views_detected')->default(0);
            
            // Earnings
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->decimal('available_balance', 12, 2)->default(0);
            
            // Timestamps
            $table->timestamp('monetisation_activated_at')->nullable();
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['user_id']);
            
            // Indexes
            $table->index(['is_monetisation_active', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporter_monetisation');
    }
};