<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveNotesColumnsFromAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('firearms', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
        Schema::table('bullets', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
        Schema::table('shoots', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
        Schema::table('purposes', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
        Schema::table('ranges', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('firearms', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
        Schema::table('bullets', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
        Schema::table('shoots', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
        Schema::table('inventories', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
        Schema::table('purposes', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
        Schema::table('ranges', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
        Schema::table('stores', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
        Schema::table('trips', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });
    }
}
