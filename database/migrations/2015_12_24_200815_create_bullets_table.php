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
            $table->timestamps();
        });

        Schema::create('bullet_cartridge', function (Blueprint $table) {
            $table->integer('bullet_id')->unsigned()->index();
            $table->foreign('bullet_id')->references('id')->on('bullets')->onDelete('cascade');
            $table->integer('cartridge_id')->unsigned()->index();
            $table->foreign('cartridge_id')->references('id')->on('cartridges')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bullet_cartridge');
        Schema::drop('bullets');
    }
}
