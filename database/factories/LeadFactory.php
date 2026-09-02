<?php

namespace Database\Factories;

use App\Models\Brocker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'developer_id' => fake()->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'uptown_id' => fake()->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'sale_person_id' => fake()->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'brocker_id' => Brocker::factory(),
            'brocker_start_date' => fake()->date(),
            'lead_name' => fake()->name(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'status' => fake()->randomElement(['closed','pending','lost','in_progress']),
        ];
    }
}
