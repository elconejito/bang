<?php
namespace Database\Seeders;

use App\Models\Reference\PrimerType;
use App\Models\User;
use Illuminate\Database\Seeder;


class PrimerTypeSeeder extends Seeder
{
    private $types = [
        [
            'label' => 'Berdan'
        ],
        [
            'label' => 'Boxer'
        ],
        [
            'label' => 'Rimfire'
        ],
    ];

    public function run()
    {
        $this->command->info('Starting PrimerType seeder');

        $user = User::where('email', env("TEST_EMAIL", "test@test.com"))->first();

        collect($this->types)->each(function ($data) use($user) {
            PrimerType::create(array_merge(['user_id' => $user->id], $data));
        });

        $this->command->info('Finished PrimerType seeder');
    }
}
