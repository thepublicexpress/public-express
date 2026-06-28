<?php
// database/migrations/2026_06_28_000004_create_hyperlocal_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hyperlocal_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('distance', 10, 2)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['user_id', 'is_read']);
            $table->index(['news_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hyperlocal_notifications');
    }
};