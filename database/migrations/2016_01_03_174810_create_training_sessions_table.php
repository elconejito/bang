<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTrainingSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms.training_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('trip_id');
            $table->integer('rounds');
            $table->integer('firearm_id');
            $table->integer('ammunition_id');
            $table->integer('user_id');
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
        Schema::drop('cms.training_sessions');
    }
}
