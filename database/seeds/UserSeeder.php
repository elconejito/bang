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
            "email" => "test@test.com",
            "name" => "Testy McTest",
            "password" => Hash::make("password"),
        ]);

        $this->command->info("Finished User seeder");
    }
}
