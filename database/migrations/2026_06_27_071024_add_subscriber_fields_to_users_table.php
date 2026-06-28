<?php
// database/migrations/2026_06_27_000101_add_subscriber_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'mobile_verified_at')) {
                $table->timestamp('mobile_verified_at')->nullable()->after('phone_verified_at');
            }
            
            if (!Schema::hasColumn('users', 'otp_verified')) {
                $table->boolean('otp_verified')->default(false)->after('mobile_verified_at');
            }
            
            if (!Schema::hasColumn('users', 'subscriber_id')) {
                $table->string('subscriber_id')->nullable()->unique()->after('reporter_id');
            }
            
            if (!Schema::hasColumn('users', 'is_subscriber')) {
                $table->boolean('is_subscriber')->default(false)->after('otp_verified');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mobile_verified_at', 'otp_verified', 'subscriber_id', 'is_subscriber']);
        });
    }
};