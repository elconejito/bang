<?php

namespace Database\Factories;

use App\Models\Magazine;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Magazine>
 */
class MagazineFactory extends Factory
{
    protected $model = Magazine::class;

    public function definition(): array
    {
        return [
            'manufacturer' => fake()->company(),
            'model_name' => fake()->lexify('Model ???'),
            'label' => fake()->words(2, true),
            'capacity' => fake()->numberBetween(5, 30),
            'loaded_rounds' => 0,
            'loaded_ammunition_id' => null,
            'location_id' => null,
            'current_firearm_id' => null,
            'user_id' => User::factory(),
        ];
    }
}
