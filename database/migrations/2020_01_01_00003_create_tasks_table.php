<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uri_access',255)->unique();
            $table->string('label',64);           
            $table->text('html_attr')->nullable();
            $table->string('icon',64)->nullable();
            $table->string('group',64)->nullable();
            $table->string('position',64)->nullable();
            $table->tinyInteger('is_visible')->default(1);
            $table->tinyInteger('is_protected')->default(1);
            $table->tinyInteger('quick_access')->default(0);
            $table->string('user_type',64)->nullable();
            $table->tinyInteger('menu_order')->default(0);
            $table->string('description',255)->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
