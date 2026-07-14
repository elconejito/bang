<?php

namespace Database\Factories;

use App\Models\Firearm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Firearm>
 */
class FirearmFactory extends Factory
{
    protected $model = Firearm::class;

    public function definition(): array
    {
        return [
            'manufacturer' => fake()->company(),
            'model' => fake()->lexify('Model ???'),
            'customizer' => null,
            'custom_package' => null,
            'label' => fake()->words(2, true),
            'user_id' => User::factory(),
        ];
    }
}
