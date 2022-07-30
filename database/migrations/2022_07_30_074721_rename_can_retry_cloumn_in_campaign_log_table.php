<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCanRetryCloumnInCampaignLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_logs', function (Blueprint $table) {
            $table->renameColumn('canRetry', 'can_retry');
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
            $table->renameColumn('can_retry', 'canRetry');
        });
    }
}
