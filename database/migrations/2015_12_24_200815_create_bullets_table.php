<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bullets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('manufacturer');
            $table->string('model');
            $table->integer('weight');
            $table->integer('inventory')->unsigned();
            $table->string('purpose');
            $table->integer('cartridge_id')->unsigned();
            $table->timestamps();

            $table->foreign('cartridge_id')
                ->references('id')
                ->on('cartridges')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bullets');
    }
}
