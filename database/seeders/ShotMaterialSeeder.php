<?php
namespace Database\Seeders;

use App\Models\Reference\ShotMaterial;
use Illuminate\Database\Seeder;

class ShotMaterialSeeder extends Seeder
{
    private $types = [
        [
            'label' => 'Steel',
        ],
        [
            'label' => 'Lead',
        ],
    ];

    public function run()
    {
        $this->command->info('Starting ShotMaterial seeder');

        collect($this->types)->each(function ($data) {
            ShotMaterial::create($data);
        });

        $this->command->info('Finished ShotMaterial seeder');
    }
}
