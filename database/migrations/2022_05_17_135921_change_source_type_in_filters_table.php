<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSourceTypeInFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('filters', function (Blueprint $table) {
            $table->dropColumn('source');
            $table->text('short_name');
            $table->text('field');
            $table->text('operation');
            $table->longText('value');
            $table->longText('query');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('filters', function (Blueprint $table) {
            $table->string('source')->change();
        });
    }
}
