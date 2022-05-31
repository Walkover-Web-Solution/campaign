<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameChannelTypeConditionToChannelTypeEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channel_type_condition', function (Blueprint $table) {
            $table->renameColumn('condition_id', 'event_id');
        });
        Schema::rename('channel_type_condition', 'channel_type_event');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('channel_type_event','channel_type_condition');
    }
}
