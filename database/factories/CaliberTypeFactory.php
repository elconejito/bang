<?php

namespace Database\Factories;

use App\Models\Reference\CaliberType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaliberType>
 */
class CaliberTypeFactory extends Factory
{
    protected $model = CaliberType::class;

    public function definition(): array
    {
        return [
            'label' => fake()->unique()->word(),
            'user_id' => User::factory(),
        ];
    }
}
