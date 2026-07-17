<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemReferenceDataSeeder extends Seeder
{
    /**
     * @var array<string, list<array{label: string, abbreviation?: string}>>
     */
    private const DEFINITIONS = [
        'reference.ammunition_casings' => [
            ['label' => 'Aluminum'], ['label' => 'Brass'], ['label' => 'Nickel'], ['label' => 'Steel'],
        ],
        'reference.ammunition_conditions' => [
            ['label' => 'Factory New'], ['label' => 'Reloaded'], ['label' => 'Factory Seconds'],
        ],
        'reference.bullet_types' => [
            ['label' => 'Frangible', 'abbreviation' => 'Frangible'],
            ['label' => 'Full Metal Jacket', 'abbreviation' => 'FMJ'],
            ['label' => 'Hollow Point', 'abbreviation' => 'HP'],
            ['label' => 'Lead Round Nose', 'abbreviation' => 'LRN'],
            ['label' => 'Soft Point', 'abbreviation' => 'SP'],
        ],
        'reference.caliber_types' => [
            ['label' => 'Centerfire'], ['label' => 'Rimfire'], ['label' => 'Shotgun'],
        ],
        'reference.location_types' => [
            ['label' => 'Range'],
        ],
        'reference.primer_types' => [
            ['label' => 'Berdan'], ['label' => 'Boxer'], ['label' => 'Rimfire'],
        ],
        'reference.shell_lengths' => [
            ['label' => '1-3/4"'], ['label' => '2-3/4"'], ['label' => '3"'], ['label' => '3-1/2"'],
        ],
        'reference.shell_types' => [
            ['label' => 'Slug'], ['label' => '#000 Buckshot'], ['label' => '#00 Buckshot'],
            ['label' => '#0 Buckshot'], ['label' => '#1 Buckshot'], ['label' => '#2 Buckshot'],
            ['label' => '#3 Buckshot'], ['label' => '#4 Buckshot'], ['label' => 'Birdshot'],
        ],
        'reference.shot_materials' => [
            ['label' => 'Steel'], ['label' => 'Lead'],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::DEFINITIONS as $table => $rows) {
            foreach ($rows as $row) {
                $query = DB::table($table)->where('label', $row['label']);

                if ($query->exists()) {
                    $query->update([...$row, 'updated_at' => now()]);
                } else {
                    DB::table($table)->insert([...$row, 'created_at' => now(), 'updated_at' => now()]);
                }
            }
        }
    }
}
