<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserOwnerToAllModels extends Migration
{
    public $models = [
        'bullets',
        'cartridges',
        'firearms',
        'inventories',
        'magazines',
        'orders',
        'pictures',
        'purposes',
        'ranges',
        'shoots',
        'stores',
        'targets',
        'trips',
    ];

    /**
     * Run the migrations.
     * Add users
     *
     * @return void
     */
    public function up()
    {
        foreach ( $this->models as $model ) {
            Schema::table($model, function (Blueprint $table) {
                $table->integer('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ( $this->models as $model ) {
            Schema::table($model, function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
}
