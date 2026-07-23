<?php

namespace Database\Factories;

use App\Models\Mount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Mount> */
class MountFactory extends Factory
{
    protected $model = Mount::class;

    public function definition(): array
    {
        return [
            'manufacturer' => fake()->company(),
            'label' => fake()->words(2, true),
            'height' => fake()->randomElement(['1.57"', '1.93"', null]),
            'mount_type' => fake()->randomElement(['picatinny', 'mlok', 'keymod', null]),
            'user_id' => User::factory(),
        ];
    }
}
