<?php

namespace Database\Factories;

use App\Models\Ammunition;
use App\Models\Firearm;
use App\Models\SessionLine;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SessionLine>
 */
class SessionLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'training_session_id' => TrainingSession::factory(),
            'firearm_id' => Firearm::factory(),
            'ammunition_id' => Ammunition::factory(),
            'suppressor_id' => null,
            'rounds' => fake()->numberBetween(50, 500),
            'deduct_ammo' => true,
            'add_firearm_count' => true,
            'add_suppressor_count' => true,
            'user_id' => User::factory(),
        ];
    }
}
