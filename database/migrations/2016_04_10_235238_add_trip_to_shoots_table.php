<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTripToShootsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shoots', function (Blueprint $table) {
            $table->integer('trip_id')->after('id');
            $table->dropColumn('range_id');
            $table->dropColumn('shoot_date');
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
            $table->dropColumn('trip_id');
            $table->integer('range_id')->after('rounds');
            $table->date('shoot_date')->after('bullet_id');
        });
    }
}
