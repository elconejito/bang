<?php

namespace Database\Factories;

use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrainingSession>
 */
class TrainingSessionFactory extends Factory
{
    protected $model = TrainingSession::class;

    public function definition(): array
    {
        return [
            'label' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'session_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'user_id' => User::factory(),
        ];
    }
}
