<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            if (!Schema::hasColumn('polls', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
            if (!Schema::hasColumn('polls', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('polls', 'start_date')) {
                $table->dateTime('start_date')->nullable()->after('description');
            }
            if (!Schema::hasColumn('polls', 'end_date')) {
                $table->dateTime('end_date')->nullable()->after('start_date');
            }
        });

        DB::table('polls')->whereNull('title')->update([
            'title' => 'उत्तर प्रदेश विधानसभा चुनाव 2027 Opinion Poll',
            'description' => 'उत्तर प्रदेश विधानसभा चुनाव 2027 opinion poll का हिस्सा बनें।',
        ]);
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            foreach (['end_date', 'start_date', 'description', 'title'] as $column) {
                if (Schema::hasColumn('polls', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
