<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditShootsTableAddShootDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoots', function (Blueprint $table) {
            $table->date('shoot_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shoots', function (Blueprint $table) {
            $table->dropColumn('shoot_date');
        });
    }
}
