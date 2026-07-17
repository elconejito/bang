<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('idam.users', function (Blueprint $table): void {
            $table->uuid('auth_uuid')->nullable(false)->change();
            $table->unique('auth_uuid');
        });
    }

    public function down(): void
    {
        Schema::table('idam.users', function (Blueprint $table): void {
            $table->dropUnique(['auth_uuid']);
            $table->uuid('auth_uuid')->nullable()->change();
        });
    }
};
