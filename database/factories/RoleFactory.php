<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word,
        ];
    }

    public static function generateModeratorRoles()
    {
        $routes = [
            'posts',
            'categories',
            'comments',
            'profiles',
            'roles',
            'tags',
            'users'
        ];

        $roles = [];

        foreach ($routes as $route) {
            $roles[] = [
                'title' => 'moderator_' . $route,
            ];
        }

        return $roles;
    }

}
