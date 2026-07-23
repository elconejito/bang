<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms.lights', function (Blueprint $table) {
            $table->string('laser')->nullable();
            $table->string('beam_pattern')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cms.lights', function (Blueprint $table) {
            $table->dropColumn(['laser', 'beam_pattern']);
        });
    }
};
