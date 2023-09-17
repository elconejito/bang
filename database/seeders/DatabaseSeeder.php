<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            AmmunitionCasingSeeder::class,
            AmmunitionConditionSeeder::class,
            BulletTypeSeeder::class,
            CaliberTypeSeeder::class,
            CaliberSeeder::class,
            LocationTypeSeeder::class,
            PrimerTypeSeeder::class,
            PurposeSeeder::class,
            ShellLengthSeeder::class,
            ShellTypeSeeder::class,
            ShotMaterialSeeder::class,
        ]);
    }
}
