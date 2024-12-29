<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            TagSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
//            ProfileSeeder::class,
//            PostSeeder::class,
//            CommentSeeder::class,

        ]);

        // создаём юзеря который может всё читать
        $user = User::factory()->create();
        $role = Role::create(['title' => 'moderator_reader']);
        $user->roles()->attach($role->id);
        // находим все пермишены у которых есть index
        $permissionIds = Permission::where('title', 'like', '%index%')->pluck('id');
        $role->permissions()->attach($permissionIds);

    }
}
