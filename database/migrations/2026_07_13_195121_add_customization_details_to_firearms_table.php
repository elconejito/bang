<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms.firearms', function (Blueprint $table) {
            $table->string('customizer')->nullable()->after('model');
            $table->string('custom_package')->nullable()->after('customizer');
        });
    }

    public function down(): void
    {
        Schema::table('cms.firearms', function (Blueprint $table) {
            $table->dropColumn(['customizer', 'custom_package']);
        });
    }
};
