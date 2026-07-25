<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reference.colors', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();
        });

        foreach (['cms.firearms', 'cms.suppressors', 'cms.optics', 'cms.lights', 'cms.misc_accessories'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('color_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['cms.firearms', 'cms.suppressors', 'cms.optics', 'cms.lights', 'cms.misc_accessories'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['color_id']);
                $table->dropColumn('color_id');
            });
        }

        Schema::dropIfExists('reference.colors');
    }
};
