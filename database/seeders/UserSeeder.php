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

        $name     = config('app.test_user_name');
        $email    = config('app.test_user_email');
        $password = config('app.test_user_password');

        if (empty($name) || empty($email) || empty($password)) {
            $this->command->error('Required configuration values missing. Please provide name, email and password.');

            return;
        }

        $user = User::create([
            "email"    => $email,
            "name"     => $name,
            "password" => Hash::make($password),
        ]);

        $this->command->info("Finished User seeder");
    }
}
