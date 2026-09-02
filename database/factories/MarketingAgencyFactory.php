<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketingAgency>
 */
class MarketingAgencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' =>fake()->company(),
            'email' =>fake()->email(),
            'phone' => fake()->numberBetween(1000000000, 1999999999),
            'start_date' => fake()->date(),
            'end_date' => fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
            'total_leads' => fake()->numberBetween(0, 99),
        ];
    }
}
