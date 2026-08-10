<?php

namespace Database\Factories;

use App\Models\Light;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Light>
 */
class LightFactory extends Factory
{
    protected $model = Light::class;

    public function definition(): array
    {
        return [
            'manufacturer' => fake()->company(),
            'label' => fake()->words(2, true),
            'model_number' => fake()->bothify('??-####'),
            'lumens' => fake()->numberBetween(200, 1500),
            'battery_type' => fake()->randomElement(['CR123', 'AA', '18650', null]),
            'user_id' => User::factory(),
        ];
    }
}
