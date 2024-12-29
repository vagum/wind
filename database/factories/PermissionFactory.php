<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
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

    public static function generatePermissionActions()
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

        $actions = ['index', 'store', 'show', 'update', 'destroy'];

        $permissions = [];

        foreach ($routes as $route) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'title' => $route . '_' . $action,
                ];
            }
        }

        return $permissions;
    }
}
