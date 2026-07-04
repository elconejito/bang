<?php
namespace Database\Seeders;

use App\Models\Reference\ShellLength;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShellLengthSeeder extends Seeder
{
    private $types = [
        [
            'label' => '1-3/4"',
        ],
        [
            'label' => '2-3/4"',
        ],
        [
            'label' => '3"',
        ],
        [
            'label' => '3-1/2"',
        ],
    ];

    public function run()
    {
        $this->command->info('Starting ShellLength seeder');

        $user = User::where('email', env("TEST_EMAIL", "test@test.com"))->first();

        collect($this->types)->each(function ($data) use($user) {
            ShellLength::create(array_merge(['user_id' => $user->id], $data));
        });

        $this->command->info('Finished ShellLength seeder');
    }
}
