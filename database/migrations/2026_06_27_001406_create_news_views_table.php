<?php
// database/migrations/2026_06_27_000002_create_news_views_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->string('referer')->nullable();
            
            // View tracking flags
            $table->boolean('is_unique')->default(true);
            $table->boolean('is_fake')->default(false);
            $table->text('fake_reason')->nullable();
            
            // View timestamp
            $table->timestamp('viewed_at')->useCurrent();
            
            // Indexes for faster queries
            $table->index(['news_id', 'ip_address', 'viewed_at']);
            $table->index(['news_id', 'session_id']);
            $table->index(['is_fake', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_views');
    }
};