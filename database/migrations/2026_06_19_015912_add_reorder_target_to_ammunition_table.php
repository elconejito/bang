<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('pgsql')->table('cms.ammunition', function (Blueprint $table) {
            $table->unsignedInteger('reorder_target')->nullable()->after('reorder_min');
        });
    }

    public function down(): void
    {
        Schema::connection('pgsql')->table('cms.ammunition', function (Blueprint $table) {
            $table->dropColumn('reorder_target');
        });
    }
};
