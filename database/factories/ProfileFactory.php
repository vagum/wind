<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'user_id' => User::inRandomOrder()->first()->id,
            'address' => fake()->address,
            'phone' => fake()->phoneNumber,
            'avatar' => fake()->imageUrl,
            'description' => fake()->text,
            'gender' => fake()->randomElement(['male', 'female']),
            'birthed_at' => fake()->dateTime,
        ];
    }
}
