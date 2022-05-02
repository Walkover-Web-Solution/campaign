<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateChannelConditionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channel_conditions', function (Blueprint $table) {
            $table->foreignId('condition_id')->change();
            $table->foreignId('channel_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channel_conditions', function (Blueprint $table) {
            $table->foreignId('condition_id')->change();
            $table->foreignId('channel_id')->change();
        });
    }
}
