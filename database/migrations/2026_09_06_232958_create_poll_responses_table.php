<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('poll_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained('poll_questions')->onDelete('cascade');
            $table->foreignId('seat_id')->constrained('assembly_seats')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('selected_option'); // value from options
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Ensure one vote per (poll, seat, question, ip) 
            $table->unique(['poll_id', 'seat_id', 'question_id', 'ip_address']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('poll_responses');
    }
};