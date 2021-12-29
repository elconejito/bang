<?php
namespace Database\Seeders;

use App\Models\Reference\AmmunitionCondition;
use Illuminate\Database\Seeder;

class AmmunitionConditionSeeder extends Seeder
{
    private $types = [
        [
            'label' => 'Factory New',
        ],
        [
            'label' => 'Reloaded',
        ],
        [
            'label' => 'Factory Seconds',
        ],
    ];

    public function run()
    {
        $this->command->info('Starting AmmunitionCondition seeder');

        collect($this->types)->each(function ($data) {
            AmmunitionCondition::create($data);
        });

        $this->command->info('Finished AmmunitionCondition seeder');
    }
}
