<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToAmmunition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ammunitions', function (Blueprint $table) {
            $table->integer('bullet_type_id')->nullable()->after('caliber_id');
            $table->integer('ammunition_casing_id')->nullable()->after('inventory');
            $table->integer('ammunition_condition_id')->nullable()->after('ammunition_casing_id');
            $table->integer('primer_type_id')->nullable()->after('bullet_type_id');
            $table->integer('shot_material_id')->nullable()->after('primer_type_id');
            $table->integer('shell_length_id')->nullable()->after('shot_material_id');
            $table->integer('shell_type_id')->nullable()->after('shell_length_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ammunitions', function (Blueprint $table) {
            $table->dropColumn('bullet_type_id');
            $table->dropColumn('ammunition_casing_id');
            $table->dropColumn('ammunition_condition_id');
            $table->dropColumn('primer_type_id');
            $table->dropColumn('shot_material_id');
            $table->dropColumn('shell_length_id');
            $table->dropColumn('shell_type_id');
        });
    }
}
