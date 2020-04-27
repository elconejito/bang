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
            $table->string('label', 100);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('bullet_id', 'ammunition_id');
        });

        Schema::table('training_sessions', function (Blueprint $table) {
            $table->renameColumn('bullet_id', 'ammunition_id');
        });

        // Drop Columns
        Schema::table('firearms', function (Blueprint $table) {
            $table->dropColumn('cartridge_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // UnDrop Columns
        Schema::table('firearms', function (Blueprint $table) {
            $table->integer('cartridge_id');
        });

        // Rename columns
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->renameColumn('ammunition_id', 'bullet_id');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->renameColumn('ammunition_id', 'bullet_id');
        });

        Schema::table('ammunitions', function (Blueprint $table) {
            $table->renameColumn('caliber_id', 'cartridge_id');
            $table->dropColumn('label');
        });

        // Rename Tables
        Schema::rename('training_sessions', 'shoots');
        Schema::rename('ammunitions', 'bullets');
    }
}
