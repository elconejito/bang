<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToCartridge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cartridges', function (Blueprint $table) {
            $table->renameColumn('size', 'caliber');
            $table->integer('cartridge_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cartridges', function (Blueprint $table) {
            $table->renameColumn('caliber', 'size');
            $table->dropColumn('cartridge_type_id');
        });
    }
}
