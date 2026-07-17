<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('idam.users')
            ->whereNull('auth_uuid')
            ->orderBy('id')
            ->eachById(function (object $user): void {
                DB::table('idam.users')
                    ->where('id', $user->id)
                    ->update(['auth_uuid' => (string) Str::uuid()]);
            });
    }

    public function down(): void
    {
        DB::table('idam.users')->update(['auth_uuid' => null]);
    }
};
