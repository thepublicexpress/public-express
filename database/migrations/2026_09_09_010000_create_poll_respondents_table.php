<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('poll_respondents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('seat_id')->constrained('assembly_seats')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('respondent_name', 100)->nullable();
            $table->string('respondent_mobile', 10)->nullable();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->unique(['poll_id', 'ip_address']);
            $table->index(['poll_id', 'seat_id']);
        });

        DB::table('poll_responses')
            ->select('poll_id', 'seat_id', 'user_id', 'respondent_name', 'respondent_mobile', 'ip_address', 'user_agent')
            ->whereNotNull('ip_address')
            ->orderBy('id')
            ->get()
            ->each(function ($response) {
                DB::table('poll_respondents')->insertOrIgnore([
                    'poll_id' => $response->poll_id,
                    'seat_id' => $response->seat_id,
                    'user_id' => $response->user_id,
                    'respondent_name' => $response->respondent_name,
                    'respondent_mobile' => $response->respondent_mobile,
                    'ip_address' => $response->ip_address,
                    'user_agent' => $response->user_agent,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down()
    {
        Schema::dropIfExists('poll_respondents');
    }
};