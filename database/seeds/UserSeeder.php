<?php

use App\Models\User;
use Illuminate\Database\Seeder;

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
            "name" => "Testy McTest",
        ]);

        $this->command->info("Finished User seeder");
    }
}
