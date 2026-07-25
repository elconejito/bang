<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms.locations', function (Blueprint $table): void {
            $table->foreignId('parent_location_id')
                ->nullable()
                ->constrained('cms.locations')
                ->restrictOnDelete();
            $table->index(
                ['user_id', 'parent_location_id'],
                'locations_user_parent_location_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('cms.locations', function (Blueprint $table): void {
            $table->dropIndex('locations_user_parent_location_index');
            $table->dropConstrainedForeignId('parent_location_id');
        });
    }
};
