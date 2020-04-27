<?php

use App\Models\Caliber;
use App\Models\CaliberType;
use App\Models\User;
use Illuminate\Database\Seeder;

class CaliberSeeder extends Seeder
{
    private $calibers = [
        [
            "label" => ".22LR",
            "short_label" => ".22LR",
            "caliber_type_id" => CaliberType::RIMFIRE,
        ],
        [
            "label" => "300 AAC Blackout",
            "short_label" => ".300 BLK",
            "caliber_type_id" => CaliberType::CENTERFIRE,
        ],
        [
            "label" => ".223 Remington",
            "short_label" => ".223",
            "caliber_type_id" => CaliberType::CENTERFIRE,
        ],
        [
            "label" => "5.56×45mm NATO",
            "short_label" => "5.56",
            "caliber_type_id" => CaliberType::CENTERFIRE,
        ],
        [
            "label" => "9mm Luger",
            "short_label" => "9mm",
            "caliber_type_id" => CaliberType::CENTERFIRE,
        ],
        [
            "label" => ".45 ACP",
            "short_label" => ".45 ACP",
            "caliber_type_id" => CaliberType::CENTERFIRE,
        ],
        [
            "label" => "20 Gauge",
            "short_label" => "20 Gauge",
            "caliber_type_id" => CaliberType::SHOTGUN,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting Caliber seeder');

        $user = User::where('email', 'test@test.com')->first();

        collect($this->calibers)->each(function ($cal) use($user) {
            $cal['user_id'] = $user->id;
            Caliber::create($cal);
        });

        $this->command->info('Finished Caliber seeder');
    }
}
