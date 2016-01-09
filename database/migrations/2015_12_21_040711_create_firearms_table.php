<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirearmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firearms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label');
            $table->string('manufacturer');
            $table->string('model');
            $table->integer('cartridge_id');
            $table->text('notes');
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
        Schema::drop('firearms');
    }
}
