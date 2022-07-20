<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionLogRefIdRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_log_ref_id_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_log_id');
            $table->string('ref_id');
            $table->string('status');
            $table->json('response')->nullable();
            $table->bigInteger('no_of_records');
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
        Schema::dropIfExists('action_log_ref_id_relations');
    }
}
