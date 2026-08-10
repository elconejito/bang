<?php

namespace Database\Factories;

use App\Models\MiscAccessory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MiscAccessory>
 */
class MiscAccessoryFactory extends Factory
{
    protected $model = MiscAccessory::class;

    public function definition(): array
    {
        return [
            'manufacturer' => fake()->company(),
            'label' => fake()->words(2, true),
            'model_number' => fake()->bothify('??-####'),
            'sub_type' => fake()->randomElement(['sling', 'holster', 'stock', 'grip', 'foregrip']),
            'user_id' => User::factory(),
        ];
    }
}
