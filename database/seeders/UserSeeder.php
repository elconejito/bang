<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info("Starting User seeder");

        $user = User::create([
            "email"    => config('app.test_user_email', 'test@test.com'),
            "name"     => config('app.test_user_name', 'Testy McTest'),
            "password" => Hash::make(config('app.test_user_password', 'password')),
        ]);

        $this->command->info("Finished User seeder");
    }
}
