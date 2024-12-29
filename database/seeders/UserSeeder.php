<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Image;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Генерация пользователей вместе с их профилями, постами и комментариями
        User::factory(10)
            ->create()
            ->each(function ($user) {

                // Предполагается, что мы уже сгенерировали роли в таблице `roles`.
                $roles = Role::all(); // Получить все доступные роли из таблицы

                // Генерация от 1 до 3 случайных ролей для каждого пользователя
                $randomRoles = $roles->random(rand(1, 3))->pluck('id'); // Выбор случайных ролей и извлечение их id
                $user->roles()->syncWithoutDetaching($randomRoles); // Добавляем роли, не удаляя предыдущие

                // Генерация случайного количества профилей для каждого пользователя
                $profiles = Profile::factory(rand(1, 3))->create(['user_id' => $user->id]);

                // Для каждого профиля создаем посты и связанные данные
                $profiles->each(function ($profile) {
                    Post::factory(rand(1, 3))
                        ->create(['profile_id' => $profile->id])
                        ->each(function ($post) use ($profile) {

                            // Генерация комментариев для каждого поста
                            Comment::factory(rand(1, 3))->create();

                            // Генерация лайков от случайных профилей
                            $likedByProfiles = Profile::inRandomOrder()->limit(rand(1, 5))->pluck('id');
                            $post->likedProfiles()->attach($likedByProfiles, ['created_at' => now(), 'updated_at' => now()]);

                            // Генерация просмотров от случайных профилей
                            $viewedByProfiles = Profile::inRandomOrder()->limit(rand(3, 10))->pluck('id');
                            $post->viewedProfiles()->attach($viewedByProfiles, ['created_at' => now(), 'updated_at' => now()]);

                            // Добавление тегов к посту
                            $tags = Tag::inRandomOrder()->limit(rand(1, 5))->pluck('id');
                            $post->tags()->attach($tags);
                        });
                });
            });
    }
}
