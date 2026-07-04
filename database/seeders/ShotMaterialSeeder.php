<?php
namespace Database\Seeders;

use App\Models\Reference\ShotMaterial;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShotMaterialSeeder extends Seeder
{
    private $types = [
        [
            'label' => 'Steel',
        ],
        [
            'label' => 'Lead',
        ],
    ];

    public function run()
    {
        $this->command->info('Starting ShotMaterial seeder');

        $user = User::where('email', env("TEST_EMAIL", "test@test.com"))->first();

        collect($this->types)->each(function ($data) use($user) {
            ShotMaterial::create(array_merge(['user_id' => $user->id], $data));
        });

        $this->command->info('Finished ShotMaterial seeder');
    }
}
