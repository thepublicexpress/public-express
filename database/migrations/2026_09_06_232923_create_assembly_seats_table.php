<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('assembly_seats', function (Blueprint $table) {
            $table->id();
            $table->integer('seat_number')->unique();
            $table->string('seat_name');
            $table->string('district')->nullable();
            $table->string('constituency_type')->nullable();
            $table->string('current_mla')->nullable();
            $table->string('party')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assembly_seats');
    }
};