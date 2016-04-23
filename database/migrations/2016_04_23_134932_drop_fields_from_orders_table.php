<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropFieldsFromOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('boxes');
            $table->dropColumn('rounds_per_box');
            $table->dropColumn('cost_per_box');
            $table->dropColumn('bullet_id');
            $table->float('total_cost')->after('rounds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('boxes');
            $table->integer('rounds_per_box');
            $table->float('cost_per_box');
            $table->integer('bullet_id');
            $table->dropColumn('total_cost');
        });
    }
}
