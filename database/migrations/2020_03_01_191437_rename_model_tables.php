<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameModelTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Rename Tables
        Schema::rename('bullets', 'ammunitions');
        Schema::rename('shoots', 'training_sessions');

        // Rename columns
        Schema::table('ammunitions', function (Blueprint $table) {
            $table->renameColumn('cartridge_id', 'caliber_id');
        });

        Schema::table('firearms', function (Blueprint $table) {
            $table->renameColumn('cartridge_id', 'caliber_id');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('bullet_id', 'ammunition_id');
        });

        Schema::table('training_sessions', function (Blueprint $table) {
            $table->renameColumn('bullet_id', 'ammunition_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Rename columns
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->renameColumn('ammunition_id', 'bullet_id');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('ammunition_id', 'bullet_id');
        });

        Schema::table('firearms', function (Blueprint $table) {
            $table->renameColumn('caliber_id', 'cartridge_id');
        });

        Schema::table('ammunitions', function (Blueprint $table) {
            $table->renameColumn('caliber_id', 'cartridge_id');
        });

        // Rename Tables
        Schema::rename('training_sessions', 'shoots');
        Schema::rename('ammunitions', 'bullets');
    }
}