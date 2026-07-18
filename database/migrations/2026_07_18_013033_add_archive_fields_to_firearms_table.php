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
        Schema::table('cms.firearms', function (Blueprint $table) {
            $table->timestampTz('archived_at')->nullable()->index();
            $table->string('archive_reason', 50)->nullable();
            $table->text('archive_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms.firearms', function (Blueprint $table) {
            $table->dropColumn(['archived_at', 'archive_reason', 'archive_description']);
        });
    }
};
