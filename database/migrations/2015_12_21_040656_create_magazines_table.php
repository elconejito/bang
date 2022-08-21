<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMagazinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms.magazines', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->string('manufacturer');
            $table->string('model_name')->nullable();
            $table->integer('capacity');
            $table->string('serial_number')->nullable();
            $table->string('id_marking')->nullable();
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
        Schema::drop('cms.magazines');
    }
}
