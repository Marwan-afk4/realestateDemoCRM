<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Uptown>
 */
class UptownFactory extends Factory
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
            'name' => fake()->randomElement(['Uptown 1','Uptown 2','Uptown 3','Uptown 4','Uptown 5','Uptown 6','Uptown 7','Uptown 8','Uptown 9','Uptown 10']),
            'description' => fake()->randomElement(['description 1','description 2','description 3','description 4','description 5','description 6','description 7','description 8','description 9','description 10']),
            'apparment' => fake()->randomElement(['appartment 1','appartment 2','appartment 3','appartment 4','appartment 5','appartment 6','appartment 7','appartment 8','appartment 9','appartment 10']),
            'strat_price' => fake()->randomNumber(5),
            'delivery_date' => fake()->dateTime(),
            'sale_type' => fake()->randomElement(['sale','rent']),
        ];
    }
}
