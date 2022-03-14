<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id');
            $table->string('name');
            $table->string('token');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->string('throttle_limit');
            $table->string('temporary_throttle_limit');
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
        Schema::dropIfExists('company_tokens');
    }
}
