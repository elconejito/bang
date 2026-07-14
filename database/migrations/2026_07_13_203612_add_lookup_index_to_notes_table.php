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
        Schema::table('cms.notes', function (Blueprint $table) {
            $table->index(
                ['user_id', 'notable_type', 'notable_id', 'created_at'],
                'notes_owner_notable_created_at_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms.notes', function (Blueprint $table) {
            $table->dropIndex('notes_owner_notable_created_at_index');
        });
    }
};
