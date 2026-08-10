<?php

namespace Database\Factories;

use App\Models\Optic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Optic>
 */
class OpticFactory extends Factory
{
    protected $model = Optic::class;

    public function definition(): array
    {
        return [
            'manufacturer' => fake()->company(),
            'label' => fake()->words(2, true),
            'model_number' => fake()->bothify('??-####'),
            'optic_type' => fake()->randomElement(['red_dot', 'prism', 'lpvo', 'variable']),
            'battery_type' => fake()->randomElement(['CR1632', 'CR2032', 'AA', null]),
            'user_id' => User::factory(),
        ];
    }
}
