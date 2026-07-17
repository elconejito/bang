<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * @var array<string, array{references: array<string, string>, rows: list<array{label: string, abbreviation?: string}>}>
     */
    private const DEFINITIONS = [
        'reference.ammunition_casings' => [
            'references' => ['cms.ammunition' => 'ammunition_casing_id'],
            'rows' => [
                ['label' => 'Aluminum'],
                ['label' => 'Brass'],
                ['label' => 'Nickel'],
                ['label' => 'Steel'],
            ],
        ],
        'reference.ammunition_conditions' => [
            'references' => ['cms.ammunition' => 'ammunition_condition_id'],
            'rows' => [
                ['label' => 'Factory New'],
                ['label' => 'Reloaded'],
                ['label' => 'Factory Seconds'],
            ],
        ],
        'reference.bullet_types' => [
            'references' => ['cms.ammunition' => 'bullet_type_id'],
            'rows' => [
                ['label' => 'Frangible', 'abbreviation' => 'Frangible'],
                ['label' => 'Full Metal Jacket', 'abbreviation' => 'FMJ'],
                ['label' => 'Hollow Point', 'abbreviation' => 'HP'],
                ['label' => 'Lead Round Nose', 'abbreviation' => 'LRN'],
                ['label' => 'Soft Point', 'abbreviation' => 'SP'],
            ],
        ],
        'reference.caliber_types' => [
            'references' => ['cms.calibers' => 'caliber_type_id'],
            'rows' => [
                ['label' => 'Centerfire'],
                ['label' => 'Rimfire'],
                ['label' => 'Shotgun'],
            ],
        ],
        'reference.location_types' => [
            'references' => ['cms.locations' => 'location_type_id'],
            'rows' => [
                ['label' => 'Range'],
            ],
        ],
        'reference.primer_types' => [
            'references' => ['cms.ammunition' => 'primer_type_id'],
            'rows' => [
                ['label' => 'Berdan'],
                ['label' => 'Boxer'],
                ['label' => 'Rimfire'],
            ],
        ],
        'reference.shell_lengths' => [
            'references' => ['cms.ammunition' => 'shell_length_id'],
            'rows' => [
                ['label' => '1-3/4"'],
                ['label' => '2-3/4"'],
                ['label' => '3"'],
                ['label' => '3-1/2"'],
            ],
        ],
        'reference.shell_types' => [
            'references' => ['cms.ammunition' => 'shell_type_id'],
            'rows' => [
                ['label' => 'Slug'],
                ['label' => '#000 Buckshot'],
                ['label' => '#00 Buckshot'],
                ['label' => '#0 Buckshot'],
                ['label' => '#1 Buckshot'],
                ['label' => '#2 Buckshot'],
                ['label' => '#3 Buckshot'],
                ['label' => '#4 Buckshot'],
                ['label' => 'Birdshot'],
            ],
        ],
        'reference.shot_materials' => [
            'references' => ['cms.ammunition' => 'shot_material_id'],
            'rows' => [
                ['label' => 'Steel'],
                ['label' => 'Lead'],
            ],
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::DEFINITIONS as $table => $definition) {
            foreach ($definition['rows'] as $row) {
                $query = DB::table($table)->where('label', $row['label']);

                if ($query->exists()) {
                    $query->update([...$row, 'updated_at' => now()]);
                } else {
                    DB::table($table)->insert([
                        ...$row,
                        'user_id' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $duplicateLabels = DB::table($table)
                ->select('label')
                ->groupBy('label')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('label');

            foreach ($duplicateLabels as $label) {
                $ids = DB::table($table)->where('label', $label)->orderBy('id')->pluck('id');
                $canonicalId = $ids->shift();

                foreach ($definition['references'] as $referencingTable => $foreignKey) {
                    DB::table($referencingTable)->whereIn($foreignKey, $ids)->update([$foreignKey => $canonicalId]);
                }

                DB::table($table)->whereIn('id', $ids)->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Canonical reference rows and repaired relationships cannot be safely distinguished from pre-existing data.
    }
};
