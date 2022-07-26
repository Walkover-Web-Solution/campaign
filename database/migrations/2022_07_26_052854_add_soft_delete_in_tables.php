<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('action_log_ref_id_relations', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('action_logs', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('campaign_logs', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('conditions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('flow_actions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('templates', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('tokens', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('action_log_ref_id_relations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('action_logs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('campaign_logs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('conditions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('flow_actions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('templates', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('tokens', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
