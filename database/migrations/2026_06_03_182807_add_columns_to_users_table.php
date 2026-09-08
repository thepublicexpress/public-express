<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->default(4)->after('id');
            $table->foreignId('state_id')->nullable()->after('role_id');
            $table->foreignId('district_id')->nullable()->after('state_id');
            $table->foreignId('tehsil_id')->nullable()->after('district_id');
            $table->string('phone', 15)->unique()->nullable()->after('name');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->string('avatar')->nullable()->after('password');
            $table->integer('points')->default(0)->after('avatar');
            $table->decimal('wallet_balance', 10, 2)->default(0)->after('points');
            $table->string('upi_id')->nullable()->after('wallet_balance');
            $table->boolean('is_active')->default(true)->after('upi_id');
            $table->boolean('is_verified_reporter')->default(false)->after('is_active');
            $table->string('reporter_id')->nullable()->unique()->after('is_verified_reporter');
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->foreign('tehsil_id')->references('id')->on('tehsils');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['tehsil_id']);
            $table->dropColumn([
                'role_id', 'state_id', 'district_id', 'tehsil_id',
                'phone', 'phone_verified_at', 'avatar',
                'points', 'wallet_balance', 'upi_id',
                'is_active', 'is_verified_reporter', 'reporter_id',
                'deleted_at',
            ]);
        });
    }
};