<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyMagazineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('magazines', function (Blueprint $table) {
            $table->renameColumn('model', 'model_name');
            $table->string('serial_number')->nullable();
            $table->string('id_marking')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('magazines', function (Blueprint $table) {
            $table->renameColumn('model_name', 'model');
            $table->dropColumn('serial_number');
            $table->dropColumn('id_marking');
        });
    }
}
