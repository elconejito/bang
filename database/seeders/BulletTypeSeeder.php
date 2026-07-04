<?php
namespace Database\Seeders;

use App\Models\Reference\CaliberType;
use App\Models\Reference\BulletType;
use App\Models\User;
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

        $user = User::where('email', env("TEST_EMAIL", "test@test.com"))->first();

        collect($this->bulletTypes)->each(function ($data) use($user) {
            BulletType::create(array_merge(['user_id' => $user->id], $data));
        });

        $this->command->info('Finished BulletType seeder');
    }
}
