<?php
// database/migrations/2026_06_27_000100_create_otp_verifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 15);
            $table->string('email')->nullable();
            $table->string('otp', 6);
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);
            $table->integer('attempts')->default(0);
            $table->integer('resend_count')->default(0);
            $table->timestamp('last_resend_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['mobile', 'is_used']);
            $table->index(['email', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};