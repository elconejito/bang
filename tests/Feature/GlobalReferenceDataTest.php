<?php

namespace Tests\Feature;

use Database\Seeders\SystemReferenceDataSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GlobalReferenceDataTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_system_reference_data_is_global_complete_and_idempotent(): void
    {
        $this->seed(SystemReferenceDataSeeder::class);
        $this->seed(SystemReferenceDataSeeder::class);

        $this->assertSame(
            ['Centerfire', 'Rimfire', 'Shotgun'],
            DB::table('reference.caliber_types')->orderBy('label')->pluck('label')->all()
        );
        $this->assertSame(4, DB::table('reference.ammunition_casings')->count());
        $this->assertSame(3, DB::table('reference.ammunition_conditions')->count());
        $this->assertSame(5, DB::table('reference.bullet_types')->count());
        $this->assertSame(1, DB::table('reference.location_types')->count());
        $this->assertSame(3, DB::table('reference.primer_types')->count());
        $this->assertSame(4, DB::table('reference.shell_lengths')->count());
        $this->assertSame(9, DB::table('reference.shell_types')->count());
        $this->assertSame(2, DB::table('reference.shot_materials')->count());
    }

    public function test_only_user_editable_reference_data_has_user_ownership(): void
    {
        foreach ([
            'reference.ammunition_casings',
            'reference.ammunition_conditions',
            'reference.bullet_types',
            'reference.caliber_types',
            'reference.location_types',
            'reference.primer_types',
            'reference.shell_lengths',
            'reference.shell_types',
            'reference.shot_materials',
        ] as $table) {
            $this->assertFalse(Schema::hasColumn($table, 'user_id'), $table.' should be global.');
        }

        $this->assertTrue(Schema::hasColumn('reference.purposes', 'user_id'));
    }
}
