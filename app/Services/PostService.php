<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostService
{
    public static function store(array $data): Post
    {
        try {
            DB::beginTransaction();
            $post = Post::create($data['post']);
            // если в поле тагов что-то вбито
            if ($data['tags']) {
                // выгребаем что прислали и проверяем что-там
                $tagIds = TagService::storeBatch($data['tags']);
                // если какие-то таги после обработки вернулись то атачим
                $tagIds && $post->tags()->attach($tagIds);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }
        return $post;
    }

    public static function update(Post $post, array $data): Post
    {
        try {
            DB::beginTransaction();

            // Обновляем сам пост
            $post->update($data['post']);

            // Обработка тегов, если переданы
            if (array_key_exists('tags', $data)) {
                if (!empty($data['tags'])) {
                    // Получаем массив ID тегов
                    $tagIds = TagService::storeBatch($data['tags']);
                    $post->tags()->sync($tagIds);
                } else {
                    // Если теги пусты, отвязываем все теги от поста
                    $post->tags()->detach();
                }
            }

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception("Failed to update post: " . $exception->getMessage());
        }

        return $post;
    }

}
