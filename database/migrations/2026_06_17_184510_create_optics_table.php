<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cms.optics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('manufacturer');
            $table->string('label');
            $table->string('serial')->nullable();
            $table->string('optic_type')->nullable();
            $table->string('battery_type')->nullable();
            $table->unsignedBigInteger('firearm_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->unsignedBigInteger('purchase_store_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms.optics');
    }
};
