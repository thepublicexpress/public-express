<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('image'); // image, code, text
            $table->string('image')->nullable();
            $table->text('code')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('position')->default('sidebar'); // header, sidebar, in-article, footer, mobile
            $table->foreignId('state_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('district_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tehsil_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('block_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('status')->default('active'); // active, inactive, paused
            $table->integer('clicks')->default(0);
            $table->integer('impressions')->default(0);
            $table->integer('priority')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ads');
    }
};