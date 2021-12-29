<?php
namespace Database\Seeders;

use App\Models\Reference\ShellLength;
use Illuminate\Database\Seeder;

class ShellLengthSeeder extends Seeder
{
    private $types = [
        [
            'label' => '1-3/4"',
        ],
        [
            'label' => '2-3/4"',
        ],
        [
            'label' => '3"',
        ],
        [
            'label' => '3-1/2"',
        ],
    ];

    public function run()
    {
        $this->command->info('Starting ShellLength seeder');

        collect($this->types)->each(function ($data) {
            ShellLength::create($data);
        });

        $this->command->info('Finished ShellLength seeder');
    }
}
