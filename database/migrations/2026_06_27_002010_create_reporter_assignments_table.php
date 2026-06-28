<?php
// database/migrations/2026_06_27_000007_create_reporter_assignments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporter_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            
            // Location assignments
            $table->foreignId('assigned_state_id')->nullable()->constrained('states')->onDelete('set null');
            $table->foreignId('assigned_district_id')->nullable()->constrained('districts')->onDelete('set null');
            $table->foreignId('assigned_tehsil_id')->nullable()->constrained('tehsils')->onDelete('set null');
            $table->foreignId('assigned_block_id')->nullable()->constrained('blocks')->onDelete('set null');
            
            // Category assignments
            $table->foreignId('assigned_category_id')->nullable()->constrained('news_categories')->onDelete('set null');
            $table->json('assigned_categories')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('assigned_at')->useCurrent();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['reporter_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporter_assignments');
    }
};