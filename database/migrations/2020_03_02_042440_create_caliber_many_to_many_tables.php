<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaliberManyToManyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caliber_firearm', function (Blueprint $table) {
            $table->integer('caliber_id');
            $table->integer('firearm_id');
        });

        Schema::create('caliber_magazine', function (Blueprint $table) {
            $table->integer('caliber_id');
            $table->integer('magazine_id');
        });

        Schema::create('firearm_magazine', function (Blueprint $table) {
            $table->integer('firearm_id');
            $table->integer('magazine_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('caliber_magazine');
        Schema::dropIfExists('caliber_firearm');
        Schema::dropIfExists('firearm_magazine');
    }
}
