<?php

use App\Models\Reference\ShellType;
use Illuminate\Database\Seeder;

class ShellTypeSeeder extends Seeder
{
    private $types = [
        [
            'label' => 'Slug'
        ],
        [
            'label' => '#000 Buckshot'
        ],
        [
            'label' => '#00 Buckshot'
        ],
        [
            'label' => '#0 Buckshot'
        ],
        [
            'label' => '#1 Buckshot'
        ],
        [
            'label' => '#2 Buckshot'
        ],
        [
            'label' => '#3 Buckshot'
        ],
        [
            'label' => '#4 Buckshot'
        ],
        [
            'label' => 'Birdshot'
        ],
    ];

    public function run()
    {
        $this->command->info('Starting ShellType seeder');

        collect($this->types)->each(function ($data) {
            ShellType::create($data);
        });

        $this->command->info('Finished ShellType seeder');
    }
}
