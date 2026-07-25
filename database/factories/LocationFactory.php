<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'label' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'parent_location_id' => null,
            'user_id' => User::factory(),
        ];
    }

    public function childOf(Location $parent): static
    {
        return $this->state(fn (): array => [
            'parent_location_id' => $parent->id,
            'user_id' => $parent->user_id,
        ]);
    }
}
