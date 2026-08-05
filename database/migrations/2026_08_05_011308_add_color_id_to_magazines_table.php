<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms.magazines', function (Blueprint $table): void {
            $table->unsignedBigInteger('color_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('cms.magazines', function (Blueprint $table): void {
            $table->dropIndex(['color_id']);
            $table->dropColumn('color_id');
        });
    }
};
