<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('poll_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('seat_id')->constrained('assembly_seats')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('poll_questions')->onDelete('cascade');
            $table->string('option_key');
            $table->string('option_label');
            $table->integer('votes_count')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->timestamps();

            // Unique per (poll, seat, question, option)
            $table->unique(['poll_id', 'seat_id', 'question_id', 'option_key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('poll_results');
    }
};