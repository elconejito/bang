<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditInventoryFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->date('inventory_date')->after('ammunition_id');
            $table->integer('order_id')->nullable()->change();
            $table->dropColumn('boxes');
            $table->dropColumn('rounds_per_box');
            $table->dropColumn('cost');
            $table->dropColumn('cost_per_box');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('inventory_date');
            $table->integer('boxes');
            $table->integer('rounds_per_box');
            $table->float('cost_per_box');
            $table->float('cost');
        });
    }
}
