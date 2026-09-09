<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('poll_responses', function (Blueprint $table) {
            $table->string('respondent_name', 100)->nullable()->after('user_id');
            $table->string('respondent_mobile', 10)->nullable()->after('respondent_name');
            $table->index(['poll_id', 'respondent_mobile']);
        });
    }

    public function down()
    {
        Schema::table('poll_responses', function (Blueprint $table) {
            $table->dropIndex('poll_responses_poll_id_respondent_mobile_index');
            $table->dropColumn(['respondent_name', 'respondent_mobile']);
        });
    }
};