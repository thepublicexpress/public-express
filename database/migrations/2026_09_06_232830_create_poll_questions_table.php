<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('poll_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['single', 'multiple'])->default('single');
            $table->json('options'); // [{"label": "BJP", "value": "bjp"}, ...]
            $table->integer('order_number')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('poll_questions');
    }
};