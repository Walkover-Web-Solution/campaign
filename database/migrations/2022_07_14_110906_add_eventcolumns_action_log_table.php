<?php

use App\Models\ActionLog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventcolumnsActionLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('action_logs', function (Blueprint $table) {
            $table->boolean('is_complete')->default(false);
            $table->bigInteger('event_recieved')->default(0);
        });
        \DB::statement('UPDATE action_logs SET event_recieved = no_of_records, is_complete=true');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('action_logs', function (Blueprint $table) {
            $table->dropColumn('is_complete');
            $table->dropColumn('event_recieved');
        });
    }
}
