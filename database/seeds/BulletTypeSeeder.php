<?php

use App\Models\Reference\CaliberType;
use App\Models\Reference\BulletType;
use Illuminate\Database\Seeder;

class BulletTypeSeeder extends Seeder
{
    private $bulletTypes = [
        [
            "label" => "Frangible",
            "abbreviation" => "Frangible",
        ],
        [
            "label" => "Full Metal Jacket",
            "abbreviation" => "FMJ",
        ],
        [
            "label" => "Hollow Point",
            "abbreviation" => "HP",
        ],
        [
            "label" => "Lead Round Nose",
            "abbreviation" => "LRN",
        ],
        [
            "label" => "Soft Point",
            "abbreviation" => "SP",
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting BulletType seeder');

        collect($this->bulletTypes)->each(function ($cal) {
            BulletType::create($cal);
        });

        $this->command->info('Finished BulletType seeder');
    }
}
