<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFlowActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flow_actions', function (Blueprint $table) {
            $table->string('name');
            $table->json('style');
            $table->json('module_data');
            $table->renameColumn('linked_id', 'channel_id');
            $table->dropColumn(['parent_id', 'is_condition', 'linked_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flow_actions', function (Blueprint $table) {
            $table->string('name');
            $table->json('style');
            $table->json('module_type');
            $table->renameColumn('linked_id', 'channel_id');
        });
    }
}
