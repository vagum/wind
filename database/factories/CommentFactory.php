<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::inRandomOrder()->first()->id,
            'profile_id' => Profile::inRandomOrder()->first()->id,
            'parent_id' => null,
            'content' => fake()->realTextBetween(30,210),
        ];
    }
}
