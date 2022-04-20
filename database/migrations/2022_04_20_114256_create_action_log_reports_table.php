<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionLogReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_log_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_log_id');
            $table->bigInteger('total');
            $table->bigInteger('delivered');
            $table->bigInteger('failed');
            $table->bigInteger('pending');
            $table->json('additional_fields');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_log_reports');
    }
}
