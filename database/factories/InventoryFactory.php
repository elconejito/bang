<?php

namespace Database\Factories;

use App\Models\Ammunition;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        return [
            'ammunition_id' => Ammunition::factory(),
            'inventory_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'rounds' => fake()->numberBetween(20, 500),
            'cost' => fake()->randomFloat(2, 10, 200),
            'user_id' => User::factory(),
        ];
    }
}
