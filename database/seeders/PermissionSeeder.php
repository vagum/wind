<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Database\Factories\PermissionFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (PermissionFactory::generatePermissionActions() as $permission) {
            Permission::create($permission);
        }

        // Получаем все роли из базы данных
        $roles = Role::all();

        // Для каждой роли получаем разрешения на основе имени роли
        $roles->each(function ($role) {
            // Разбиваем имя роли на части, чтобы определить соответствующие разрешения
            // Например, для роли 'moderator_posts' мы будем искать разрешения 'posts_store', 'posts_show' и так далее.
            $roleName = $role->title;

            // Если роль начинается с 'moderator_' или другого паттерна, то мы назначаем соответствующие разрешения
            if (preg_match('/^(moderator)_(.*)$/', $roleName, $matches)) {
                // Вторая часть строки (после подчеркивания) — это объект, с которым будет связано разрешение, например 'posts'
                $object = $matches[2]; // Например, 'posts', 'categories', 'comments'

                // Формируем список разрешений для этого объекта
                $permissions = [
                    $object . '_index',   // Например, 'posts_index'
                    $object . '_store',   // Например, 'posts_store'
                    $object . '_show',    // Например, 'posts_show'
                    $object . '_update',  // Например, 'posts_update'
                    $object . '_destroy', // Например, 'posts_destroy'
                ];

                // Находим все соответствующие разрешения в базе данных
                $permissionIds = Permission::whereIn('title', $permissions)->pluck('id');

                // Привязываем эти разрешения к роли
                $role->permissions()->syncWithoutDetaching($permissionIds);
            }

        });
    }
}
