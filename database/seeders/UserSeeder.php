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
        if (! app()->environment('local', 'testing')) {
            return;
        }

        $name = config('app.test_user_name');
        $email = config('app.test_user_email');
        $password = config('app.test_user_password');

        if (blank($name) || blank($email) || blank($password)) {
            $this->command->info('No test user defined, skipping creation.');

            return;
        }

        User::firstOrCreate(['email' => $email], [
            'name' => $name,
            'password' => Hash::make($password),
        ]);

        $this->command->info('Finished User seeder');
    }
}
