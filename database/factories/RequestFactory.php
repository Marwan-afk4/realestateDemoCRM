<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->randomElement([3,4,5,6,7,8,9,10,11,12]),
            'request_time' => fake()->dateTime(),
            'request_type' => fake()->randomElement(['training','contract']),
        ];
    }
}
