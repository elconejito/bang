<?php
namespace Database\Seeders;

use App\Models\Reference\PrimerType;
use Illuminate\Database\Seeder;


class PrimerTypeSeeder extends Seeder
{
    private $types = [
        [
            'label' => 'Berdan'
        ],
        [
            'label' => 'Boxer'
        ],
        [
            'label' => 'Rimfire'
        ],
    ];

    public function run()
    {
        $this->command->info('Starting PrimerType seeder');

        collect($this->types)->each(function ($data) {
            PrimerType::create($data);
        });

        $this->command->info('Finished PrimerType seeder');
    }
}
