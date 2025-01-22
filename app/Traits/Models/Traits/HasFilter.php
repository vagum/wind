<?php

namespace App\Traits\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFilter
{
    public function scopeFilter($builder, $data)
    {
        // Фильтрация по заголовку
        if (!empty($data['title'])) {
            $builder->where('title', 'like', '%' . $data['title'] . '%');
        }

        // Фильтрация по содержимому
        if (!empty($data['content'])) {
            $builder->where('content', 'like', '%' . $data['content'] . '%');
        }

        // Фильтрация по дате публикации
        if (!empty($data['published_at_from'])) {
            $builder->where('published_at', '>=', $data['published_at_from']);
        }

        if (!empty($data['published_at_to'])) {
            $builder->where('published_at', '<=', $data['published_at_to']);
        }

        // Фильтрация по просмотрам
        if (!empty($data['views_from'])) {
            $builder->whereRaw(
                '(select COUNT(*) from post_profile_views where post_profile_views.post_id = posts.id) >= ?',
                [(int)$data['views_from']]
            );
        }

        if (!empty($data['views_to'])) {
            $builder->whereRaw(
                '(select COUNT(*) from post_profile_views where post_profile_views.post_id = posts.id) <= ?',
                [(int)$data['views_to']]
            );
        }

        // Фильтрация по категории
        if (!empty($data['category_title'])) {
            $builder->whereHas('category', function ($q) use ($data) {
                $q->where('title', 'like', '%' . $data['category_title'] . '%');
            });
        }

        // Фильтрация по профилю
        if (!empty($data['profile_name'])) {
            $builder->whereHas('profile', function ($q) use ($data) {
                $q->where('name', 'like', '%' . $data['profile_name'] . '%');
            });
        }

        // Фильтрация по тегам
        if (!empty($data['tags_title'])) {
            $builder->whereHas('tags', function ($q) use ($data) {
                $q->where('title', 'like', '%' . $data['tags_title'] . '%');
            });
        }

        return $builder;
    }


//    public function scopeFilter(Builder $builder, array $data, ): Builder
//    {
//        $ClassName = 'App\\Http\\Filters\\'.class_basename($this).'Filter';
//
//        // Проверка на существование вызываемого класса
//        if (class_exists($ClassName)) {
//            return (new $ClassName())->apply($data, $builder);
//        }
//        // Возвращаем builder без применения фильтра если класса нет
//        return $builder;
//    }
}
