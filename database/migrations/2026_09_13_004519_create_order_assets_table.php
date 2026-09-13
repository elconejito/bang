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
        Schema::create('cms.order_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('cms.orders')->cascadeOnDelete();
            $table->integer('user_id');
            $table->string('asset_type');
            $table->unsignedBigInteger('asset_id');
            $table->decimal('cost', 10, 2)->default(0);
            $table->timestamps();
            $table->unique(['asset_type', 'asset_id']);
            $table->index(['order_id', 'asset_type', 'asset_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms.order_assets');
    }
};
