<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateAmmunitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms.ammunition', function (Blueprint $table) {
            $table->id();
            $table->string('manufacturer');
            $table->string('label');
            $table->integer('weight')->nullable();
            $table->integer('inventory')->default(0);
            $table->integer('purpose_id')->nullable();
            $table->integer('caliber_id');
            $table->integer('bullet_type_id')->nullable();
            $table->integer('ammunition_casing_id')->nullable();
            $table->integer('ammunition_condition_id')->nullable();
            $table->integer('primer_type_id')->nullable();
            $table->integer('shot_material_id')->nullable();
            $table->integer('shell_length_id')->nullable();
            $table->integer('shell_type_id')->nullable();
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
        Schema::drop('cms.ammunition');
    }
}
