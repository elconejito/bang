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
        Schema::create('cms.session_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained('cms.training_sessions')->cascadeOnDelete();
            $table->foreignId('firearm_id')->constrained('cms.firearms');
            $table->foreignId('ammunition_id')->constrained('cms.ammunition');
            $table->foreignId('suppressor_id')->nullable()->constrained('cms.suppressors')->nullOnDelete();
            $table->integer('rounds');
            $table->boolean('deduct_ammo')->default(true);
            $table->boolean('add_firearm_count')->default(true);
            $table->boolean('add_suppressor_count')->default(true);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms.session_lines');
    }
};
