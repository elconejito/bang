<?php
namespace Database\Seeders;

use App\Models\Reference\CaliberType;
use App\Models\User;
use Illuminate\Database\Seeder;

class CaliberTypeSeeder extends Seeder
{
    private $caliberTypes = [
        [
            "label" => "Centerfire",
        ],
        [
            "label" => "Rimfire",
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

        $user = User::where('email', config('app.test_user_email'))->first();

        collect($this->caliberTypes)->each(function ($data) use($user) {
            CaliberType::create(array_merge(['user_id' => $user->id], $data));
        });

        $this->command->info('Finished CaliberType seeder');
    }
}
