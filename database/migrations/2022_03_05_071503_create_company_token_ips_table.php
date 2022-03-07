<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTokenIpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_token_ips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('token_id')->constrained();
            $table->ipAddress('ip');
            $table->foreignId('ip_type_id')->constrained('ip_types');
            $table->datetime('expires_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('company_token_ips');
    }
}
