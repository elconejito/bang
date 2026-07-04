<?php

namespace Database\Factories;

use App\Models\Reference\Purpose;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purpose>
 */
class PurposeFactory extends Factory
{
    protected $model = Purpose::class;

    public function definition(): array
    {
        return [
            'label' => fake()->unique()->word(),
            'user_id' => User::factory(),
        ];
    }
}
