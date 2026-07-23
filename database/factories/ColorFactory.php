<?php

namespace Database\Factories;

use App\Models\Reference\Color;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Color> */
class ColorFactory extends Factory
{
    protected $model = Color::class;

    public function definition(): array
    {
        return ['label' => fake()->unique()->colorName(), 'user_id' => User::factory()];
    }
}
