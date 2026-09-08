<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('news', function (Blueprint $table) {
            // Approval System Columns
            if (!Schema::hasColumn('news', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            }
            
            if (!Schema::hasColumn('news', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->after('approval_status');
            }
            
            if (!Schema::hasColumn('news', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            
            if (!Schema::hasColumn('news', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }
            
            if (!Schema::hasColumn('news', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_at');
            }
            
            if (!Schema::hasColumn('news', 'approval_level')) {
                $table->enum('approval_level', ['block', 'tehsil', 'district', 'state', 'admin'])->nullable()->after('rejection_reason');
            }
        });
    }

    public function down()
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn([
                'approval_status',
                'approved_by',
                'approved_at',
                'rejected_at',
                'rejection_reason',
                'approval_level'
            ]);
        });
    }
};