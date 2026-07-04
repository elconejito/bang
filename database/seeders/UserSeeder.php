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
    public function run()
    {
        $this->command->info("Starting User seeder");

        $user = User::create([
            "email" => env("TEST_EMAIL", "test@test.com"),
            "name"  => env("TEST_NAME", "Testy McTest"),
            "password"  => Hash::make(env("TEST_PASSWORD", "password")),
        ]);

        $this->command->info("Finished User seeder");
    }
}
