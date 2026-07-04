<?php

namespace Database\Factories;

use App\Models\Caliber;
use App\Models\Reference\CaliberType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Caliber>
 */
class CaliberFactory extends Factory
{
    protected $model = Caliber::class;

    public function definition(): array
    {
        return [
            'caliber' => fake()->numerify('#.##mm'),
            'label' => fake()->words(2, true),
            'caliber_type_id' => CaliberType::factory(),
            'user_id' => User::factory(),
        ];
    }
}
