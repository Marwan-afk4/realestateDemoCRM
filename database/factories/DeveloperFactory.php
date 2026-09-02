<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Developer>
 */
class DeveloperFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['solic','Elmorshidy','madinty','shady','Developer 5','Developer 6','Developer 7','Developer 8','Developer 9','Developer 10']),
            'email' => fake()->email(),
            'place' => fake()->city(),
            'units' => fake()->randomNumber(3),
            'total_deals' => fake()->randomNumber(3),
            'deals_done' => fake()->randomNumber(2)
        ];
    }
}
