<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->unsignedInteger('reorder_min')->nullable();
            $table->unsignedInteger('reorder_target')->nullable();
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
            $table->softDeletes();
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
            $table->string('customizer')->nullable();
            $table->string('custom_package')->nullable();
            $table->string('serial')->nullable();
            $table->integer('location_id')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->integer('purchase_store_id')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.inventories', function (Blueprint $table) {
            $table->id();
            $table->integer('rounds');
            $table->date('inventory_date');
            $table->integer('order_id')->nullable();
            $table->float('cost')->default(0);
            $table->integer('training_session_id')->nullable();
            $table->integer('ammunition_id');
            $table->integer('firearm_id')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.locations', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->text('description')->nullable();
            $table->integer('location_type_id')->nullable();
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
            $table->foreignId('loaded_ammunition_id')->nullable()->constrained('cms.ammunition')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('cms.locations')->nullOnDelete();
            $table->foreignId('current_firearm_id')->nullable()->constrained('cms.firearms')->nullOnDelete();
            $table->unsignedInteger('loaded_rounds')->default(0);
            $table->integer('user_id');
            $table->timestamps();
            $table->index(['user_id', 'manufacturer', 'model_name', 'capacity'], 'magazines_group_lookup_index');
            $table->index(['user_id', 'location_id'], 'magazines_user_location_index');
            $table->index(['user_id', 'current_firearm_id'], 'magazines_user_current_firearm_index');
        });
        DB::statement('CREATE UNIQUE INDEX magazines_one_current_per_firearm_unique ON cms.magazines (current_firearm_id) WHERE current_firearm_id IS NOT NULL');
        Schema::create('cms.notes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('note');
            $table->integer('notable_id');
            $table->string('notable_type');
            $table->timestamps();
            $table->index(
                ['user_id', 'notable_type', 'notable_id', 'created_at'],
                'notes_owner_notable_created_at_index'
            );
        });
        Schema::create('cms.orders', function (Blueprint $table) {
            $table->id();
            $table->integer('rounds')->default(0);
            $table->float('total_cost')->default(0);
            $table->integer('store_id')->nullable();
            $table->string('order_ref')->nullable();
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
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['picture_id', 'pictureable_type', 'pictureable_id'], 'pictureables_unique_attachment');
            $table->index(['pictureable_type', 'pictureable_id', 'sort_order', 'id'], 'pictureables_entity_order_index');
        });
        Schema::create('cms.pictures', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->string('name');
            $table->string('filename')->nullable();
            $table->string('disk')->default('pictures');
            $table->string('key_prefix')->nullable();
            $table->string('processing_status')->default('pending');
            $table->unsignedInteger('processing_version')->default(1);
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('byte_size')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('failure_code')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->integer('user_id');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
        Schema::table('cms.pictureables', function (Blueprint $table) {
            $table->foreign('picture_id')->references('id')->on('cms.pictures')->cascadeOnDelete();
        });
        DB::statement('CREATE UNIQUE INDEX pictureables_one_primary_per_entity_unique ON cms.pictureables (pictureable_type, pictureable_id) WHERE is_primary = true');
        Schema::create('cms.purchases', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::create('cms.ranges', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->text('description')->nullable();
            $table->string('address')->nullable();
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
            $table->foreignId('picture_id')->constrained('cms.pictures')->restrictOnDelete();
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
            $table->text('description')->nullable();
            $table->date('session_date');
            $table->integer('location_id')->nullable();
            $table->foreignId('range_id')->nullable()->constrained('cms.ranges')->nullOnDelete();
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
        Schema::dropIfExists('cms.training_sessions');
        Schema::dropIfExists('cms.targets');
        Schema::dropIfExists('cms.stores');
        Schema::dropIfExists('cms.ranges');
        Schema::dropIfExists('cms.purchases');
        Schema::dropIfExists('cms.pictureables');
        Schema::dropIfExists('cms.pictures');
        Schema::dropIfExists('cms.orders');
        Schema::dropIfExists('cms.notes');
        Schema::dropIfExists('cms.magazines');
        Schema::dropIfExists('cms.locations');
        Schema::dropIfExists('cms.inventories');
        Schema::dropIfExists('cms.firearms');
        Schema::dropIfExists('cms.cartridges');
        Schema::dropIfExists('cms.ammunition');
        Schema::dropIfExists('cms.calibers');
    }
}
