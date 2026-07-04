<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferenceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reference.ammunition_casings', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('reference.ammunition_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('reference.bullet_types', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('abbreviation');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('reference.caliber_types', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('reference.location_types', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('reference.primer_types', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('reference.purposes', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('reference.shell_lengths', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('reference.shell_types', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('reference.shot_materials', function (Blueprint $table) {
            $table->id();
            $table->string('label');
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
        Schema::dropIfExists('reference.shot_materials');
        Schema::dropIfExists('reference.shell_types');
        Schema::dropIfExists('reference.shell_lengths');
        Schema::dropIfExists('reference.purposes');
        Schema::dropIfExists('reference.primer_types');
        Schema::dropIfExists('reference.location_types');
        Schema::dropIfExists('reference.caliber_types');
        Schema::dropIfExists('reference.bullet_types');
        Schema::dropIfExists('reference.ammunition_conditions');
        Schema::dropIfExists('reference.ammunition_casings');
    }
}
