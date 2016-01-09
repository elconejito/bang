<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShootsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shoots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rounds');
            $table->integer('range_id');
            $table->integer('firearm_id');
            $table->integer('bullet_id');
            $table->date('shoot_date');
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
        Schema::drop('shoots');
    }
}
