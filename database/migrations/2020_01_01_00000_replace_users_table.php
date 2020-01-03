<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReplaceUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->string('user_id',36)->primary();
            $table->string('username',64)->unique();
            $table->string('password',255);
            $table->string('fullname',255);
            $table->string('email',255)->unique();
            $table->string('phone',30)->nullable();
            $table->text('avatar_url')->nullable();
            $table->tinyInteger('is_disabled')->default(1);
            
            $table->string('user_type',64);
            $table->string('group_name',64);
            $table->string('user_code_number',20);

            $table->text('settings')->nullable();
            $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('users');
    }
}
