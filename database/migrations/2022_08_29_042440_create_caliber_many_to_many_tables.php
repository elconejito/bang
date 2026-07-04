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
    public function up(): void
    {
        Schema::create('cms.caliber_firearm', function (Blueprint $table) {
            $table->integer('caliber_id');
            $table->integer('firearm_id');
        });

        Schema::create('cms.caliber_magazine', function (Blueprint $table) {
            $table->integer('caliber_id');
            $table->integer('magazine_id');
        });

        Schema::create('cms.firearm_magazine', function (Blueprint $table) {
            $table->integer('firearm_id');
            $table->integer('magazine_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cms.caliber_magazine');
        Schema::dropIfExists('cms.caliber_firearm');
        Schema::dropIfExists('cms.firearm_magazine');
    }
}
