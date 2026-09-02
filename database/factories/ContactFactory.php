<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    public function definition(): array
    {
        $phone = '010'.fake()->unique()->numerify('########');

        return [
            'name' => fake()->name(),
            'phone' => $phone,
            'source' => 'other',
        ];
    }
}
