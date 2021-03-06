<?php

use App\Models\CaliberType;
use Illuminate\Database\Seeder;

class CaliberTypeSeeder extends Seeder
{
    private $caliberTypes = [
        [
            "label" => "Rimfire",
        ],
        [
            "label" => "Centerfire",
        ],
        [
            "label" => "Shotgun",
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting CaliberType seeder');

        collect($this->caliberTypes)->each(function ($cal) {
            CaliberType::create($cal);
        });

        $this->command->info('Finished CaliberType seeder');
    }
}
