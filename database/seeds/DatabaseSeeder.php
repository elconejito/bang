<?php

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
            AmmunitionCasingSeeder::class,
            AmmunitionConditionSeeder::class,
            BulletTypeSeeder::class,
            CaliberTypeSeeder::class,
            CaliberSeeder::class,
            PrimerTypeSeeder::class,
            PurposeSeeder::class,
            ShellLengthSeeder::class,
            ShotMaterialSeeder::class,
        ]);
    }
}
