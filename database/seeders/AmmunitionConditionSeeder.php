<?php
namespace Database\Seeders;

use App\Models\Reference\AmmunitionCondition;
use App\Models\User;
use Illuminate\Database\Seeder;

class AmmunitionConditionSeeder extends Seeder
{
    private $types = [
        [
            'label' => 'Factory New',
        ],
        [
            'label' => 'Reloaded',
        ],
        [
            'label' => 'Factory Seconds',
        ],
    ];

    public function run()
    {
        $this->command->info('Starting AmmunitionCondition seeder');

        $user = User::where('email', env("TEST_EMAIL", "test@test.com"))->first();

        collect($this->types)->each(function ($data) use($user) {
            AmmunitionCondition::create(array_merge(['user_id' => $user->id], $data));
        });

        $this->command->info('Finished AmmunitionCondition seeder');
    }
}
