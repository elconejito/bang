<?php

namespace Database\Factories;

use App\Models\Suppressor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Suppressor>
 */
class SuppressorFactory extends Factory
{
    protected $model = Suppressor::class;

    public function definition(): array
    {
        return [
            'manufacturer' => fake()->company(),
            'label' => fake()->words(2, true),
            'serial' => fake()->bothify('??-####'),
            'is_nfa' => true,
            'mount_type' => fake()->randomElement(['1/2×28', '5/8×24', 'tri-lug', 'KeyMo']),
            'user_id' => User::factory(),
        ];
    }
}
