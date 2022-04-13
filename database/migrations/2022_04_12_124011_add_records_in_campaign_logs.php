<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecordsInCampaignLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_logs', function (Blueprint $table) {
            $table->bigInteger('sms_records');
            $table->bigInteger('email_records');
            $table->string("mongo_uid");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_logs', function (Blueprint $table) {
            $table->bigInteger('sms_records');
            $table->bigInteger('email_records');
            $table->string("mongo_uid");
        });
    }
}
