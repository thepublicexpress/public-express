<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('news_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('body');
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};