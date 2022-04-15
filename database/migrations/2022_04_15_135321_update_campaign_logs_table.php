<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCampaignLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_logs', function (Blueprint $table) {
            $table->bigInteger('no_of_contacts');
            $table->string('status');
            $table->dropColumn(['sms_records', 'email_records']);
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
            $table->bigInteger('no_of_contacts');
            $table->string('status');
            $table->dropColumn(['sms_records', 'email_records']);
        });
    }
}
