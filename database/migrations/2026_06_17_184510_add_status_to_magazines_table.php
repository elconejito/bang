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
        Schema::table('cms.magazines', function (Blueprint $table) {
            $table->string('status')->default('empty')->after('id_marking');
        });
    }

    public function down(): void
    {
        Schema::table('cms.magazines', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
