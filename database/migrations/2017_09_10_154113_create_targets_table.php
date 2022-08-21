<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms.targets', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->float('distance');
            $table->float('group_size');
            $table->integer('picture_id');
            $table->integer('bullet_id')->nullable();
            $table->integer('firearm_id')->nullable();
            $table->integer('shoot_id')->nullable();
            $table->integer('trip_id')->nullable();
            $table->integer('training_session_id')->nullable();
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
        Schema::dropIfExists('cms.targets');
    }
}
