<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrainingSubscription>
 */
class TrainingSubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->randomElement([4,5,6,7,8]),
            'full_name' => fake()->name(),
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'age' => fake()->randomNumber(2),
            'qualification' => fake()->randomElement(['bachelor','master','phd']),
            'governate' => fake()->randomElement(['cairo','giza','alex']),
            'experience_year' => fake()->randomNumber(2),
        ];
    }
}
