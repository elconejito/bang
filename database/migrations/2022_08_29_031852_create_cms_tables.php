<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms.ammunition', function (Blueprint $table) {
            $table->id();
            $table->string('manufacturer');
            $table->string('label');
            $table->integer('weight')->nullable();
            $table->integer('inventory')->default(0);
            $table->integer('purpose_id')->nullable();
            $table->integer('caliber_id');
            $table->integer('bullet_type_id')->nullable();
            $table->integer('ammunition_casing_id')->nullable();
            $table->integer('ammunition_condition_id')->nullable();
            $table->integer('primer_type_id')->nullable();
            $table->integer('shot_material_id')->nullable();
            $table->integer('shell_length_id')->nullable();
            $table->integer('shell_type_id')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.calibers', function (Blueprint $table) {
            $table->id();
            $table->string('caliber');
            $table->string('label')->nullable();
            $table->integer('caliber_type_id');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.cartridges', function (Blueprint $table) {
            $table->id();
            $table->string('caliber');
            $table->string('label');
            $table->integer('cartridge_type_id');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.firearms', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->string('manufacturer');
            $table->string('model')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.inventories', function (Blueprint $table) {
            $table->id();
            $table->integer('rounds');
            $table->integer('order_id')->nullable();
            $table->float('cost')->default(0);
            $table->integer('ammunition_id');
            $table->date('inventory_date');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.locations', function(Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('type_id');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.magazines', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->string('manufacturer');
            $table->string('model_name')->nullable();
            $table->integer('capacity');
            $table->string('serial_number')->nullable();
            $table->string('id_marking')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.notes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('note');
            $table->integer('notable_id');
            $table->string('notable_type');
            $table->timestamps();
        });
        Schema::create('cms.orders', function (Blueprint $table) {
            $table->id();
            $table->integer('rounds')->default(0);
            $table->float('total_cost')->default(0);
            $table->integer('store_id')->nullable();
            $table->date('order_date');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.pictureables', function (Blueprint $table) {
            $table->id();
            $table->integer('picture_id');
            $table->integer('pictureable_id');
            $table->string('pictureable_type');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.pictures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('filename');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.purchases', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.ranges', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.stores', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->text('description')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.targets', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->float('distance');
            $table->float('group_size');
            $table->integer('picture_id');
            $table->integer('bullet_id')->nullable();
            $table->integer('firearm_id')->nullable();
            $table->integer('shoot_id')->nullable();
            $table->integer('trip_id')->nullable();
            $table->integer('training_session_id')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.training_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('trip_id');
            $table->integer('rounds');
            $table->integer('firearm_id');
            $table->integer('ammunition_id');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.trips', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->date('trip_date');
            $table->integer('range_id');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cms.trips');
        Schema::dropIfExists('cms.training_sessions');
        Schema::dropIfExists('cms.targets');
        Schema::dropIfExists('cms.stores');
        Schema::dropIfExists('cms.ranges');
        Schema::dropIfExists('cms.purchases');
        Schema::drop('cms.pictures');
        Schema::dropIfExists('cms.pictureables');
        Schema::dropIfExists('cms.orders');
        Schema::dropIfExists('cms.notes');
        Schema::dropIfExists('cms.magazines');
        Schema::drop('cms.locations');
        Schema::drop('cms.inventories');
        Schema::dropIfExists('cms.firearms');
        Schema::dropIfExists('cms.cartridges');
        Schema::dropIfExists('cms.ammunition');
    }
}
