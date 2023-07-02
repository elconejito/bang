<?php
namespace Database\Seeders;

use App\Models\Caliber;
use App\Models\Reference\CaliberType;
use App\Models\User;
use Illuminate\Database\Seeder;

class CaliberSeeder extends Seeder
{
    private array $calibers = [
        [
            "caliber" => ".22LR",
            "label" => ".22LR",
            "caliber_type_id" => CaliberType::RIMFIRE,
        ],
        [
            "caliber" => "5.56×45mm NATO",
            "label" => "5.56",
            "caliber_type_id" => CaliberType::CENTERFIRE,
        ],
        [
            "caliber" => "9mm Luger",
            "label" => "9mm",
            "caliber_type_id" => CaliberType::CENTERFIRE,
        ],
        [
            "caliber" => "12 Gauge",
            "label" => "12G",
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

        $user = User::where('email', env("TEST_EMAIL", "test@test.com"))->first();

        collect($this->calibers)->each(function ($data) use($user) {
            Caliber::create(array_merge(['user_id' => $user->id], $data));
        });

        $this->command->info('Finished Caliber seeder');
    }
}
