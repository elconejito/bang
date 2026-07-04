<?php
namespace Database\Seeders;

use App\Models\Reference\AmmunitionCasing;
use App\Models\User;
use Illuminate\Database\Seeder;


class AmmunitionCasingSeeder extends Seeder
{
    private $types = [
        [
            'label' => 'Aluminum',
        ],
        [
            'label' => 'Brass',
        ],
        [
            'label' => 'Nickel',
        ],
        [
            'label' => 'Steel',
        ],
    ];

    public function run()
    {
        $this->command->info('Starting AmmunitionCasing seeder');

        $user = User::where('email', env("TEST_EMAIL", "test@test.com"))->first();

        collect($this->types)->each(function ($data) use($user) {
            AmmunitionCasing::create(array_merge(['user_id' => $user->id], $data));
        });

        $this->command->info('Finished AmmunitionCasing seeder');
    }
}
