<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateChannelConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('channel_conditions', 'channel_type_condition');
        Schema::table('channel_type_condition', function (Blueprint $table) {
            $table->renameColumn('channel_id', 'channel_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('channel_conditions', 'channel_type_condition');
        Schema::table('channel_type_condition', function (Blueprint $table) {
            $table->renameColumn('channel_id', 'channel_type_id');
        });
    }
}
