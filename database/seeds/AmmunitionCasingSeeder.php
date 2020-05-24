<?php

use App\Models\Reference\AmmunitionCasing;
use Illuminate\Database\Seeder;


class AmmunitionCasingSeeder extends Seeder
{
    private $types = [
        [
            'label' => 'Aluminum',
        ],
        [
            'label' => 'Brass',
        ],
        [
            'label' => 'Steel',
        ],
    ];

    public function run()
    {
        $this->command->info('Starting AmmunitionCasing seeder');

        collect($this->types)->each(function ($data) {
            AmmunitionCasing::create($data);
        });

        $this->command->info('Finished AmmunitionCasing seeder');
    }
}
