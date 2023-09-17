<?php

namespace Database\Seeders;

use App\Models\Reference\LocationType;
use App\Models\User;
use Illuminate\Database\Seeder;

class LocationTypeSeeder extends Seeder
{
    private array $locationTypes = [
        [
            'label' => 'Range',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Starting LocationType seeder');

        $user = User::where('email', env("TEST_EMAIL", "test@test.com"))->first();

        collect($this->locationTypes)->each(function ($data) use($user) {
            LocationType::create(array_merge(['user_id' => $user->id], $data));
        });

        $this->command->info('Finished LocationType seeder');
    }
}
