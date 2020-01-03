<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logins', function (Blueprint $table) {
            $table->string('id',36)->primary();
            $table->string('user_id',36);
            $table->integer('login_start');
            $table->string('ip_address',40);
            $table->text('user_agent')->nullable();
            $table->tinyInteger('logout');
            $table->string('remember_selector',40)->nullable();
            $table->string('remember_validator',255)->nullable();
            $table->dateTime('remember_expire')->nullable();
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
        Schema::dropIfExists('logins');
    }
}
