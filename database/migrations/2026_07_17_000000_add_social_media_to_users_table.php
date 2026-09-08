<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ✅ Add columns only if they don't exist (to avoid errors)
            if (!Schema::hasColumn('users', 'facebook')) {
                $table->string('facebook')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'x')) {
                $table->string('x')->nullable()->after('facebook');
            }
            if (!Schema::hasColumn('users', 'instagram')) {
                $table->string('instagram')->nullable()->after('x');
            }
            if (!Schema::hasColumn('users', 'youtube')) {
                $table->string('youtube')->nullable()->after('instagram');
            }
            if (!Schema::hasColumn('users', 'linkedin')) {
                $table->string('linkedin')->nullable()->after('youtube');
            }
            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('linkedin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'facebook',
                'x',
                'instagram',
                'youtube',
                'linkedin',
                'website'
            ]);
        });
    }
};