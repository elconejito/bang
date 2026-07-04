<?php

namespace Database\Factories;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Reference\Purpose;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ammunition>
 */
class AmmunitionFactory extends Factory
{
    protected $model = Ammunition::class;

    public function definition(): array
    {
        return [
            'manufacturer' => fake()->company(),
            'label' => fake()->words(3, true),
            'weight' => fake()->numberBetween(55, 180),
            'inventory' => 0,
            'purpose_id' => Purpose::factory(),
            'caliber_id' => Caliber::factory(),
            'user_id' => User::factory(),
        ];
    }
}
