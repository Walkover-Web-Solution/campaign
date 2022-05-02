<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCompanyTokenIpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_token_ips', function (Blueprint $table) {
            $table->renameColumn('company_token_id', 'token_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_token_ips', function (Blueprint $table) {
            $table->renameColumn('company_token_id', 'token_id');
        });
    }
}
