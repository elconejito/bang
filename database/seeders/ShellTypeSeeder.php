<?php
namespace Database\Seeders;

use App\Models\Reference\ShellType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShellTypeSeeder extends Seeder
{
    private $types = [
        [
            'label' => 'Slug'
        ],
        [
            'label' => '#000 Buckshot'
        ],
        [
            'label' => '#00 Buckshot'
        ],
        [
            'label' => '#0 Buckshot'
        ],
        [
            'label' => '#1 Buckshot'
        ],
        [
            'label' => '#2 Buckshot'
        ],
        [
            'label' => '#3 Buckshot'
        ],
        [
            'label' => '#4 Buckshot'
        ],
        [
            'label' => 'Birdshot'
        ],
    ];

    public function run()
    {
        $this->command->info('Starting ShellType seeder');

        $user = User::where('email', env("TEST_EMAIL", "test@test.com"))->first();

        collect($this->types)->each(function ($data) use($user) {
            ShellType::create(array_merge(['user_id' => $user->id], $data));
        });

        $this->command->info('Finished ShellType seeder');
    }
}
