<?php

namespace App\Traits\Models\Traits\Stats;

use Illuminate\Database\Eloquent\Builder;

trait HasFilter
{
    public function scopeFilter($builder, $data)
    {

        // Фильтрация по дате генерации статов
        if (!empty($data['date_from'])) {
            $builder->where('date', '>=', $data['date_from']);
        }

        if (!empty($data['date_to'])) {
            $builder->where('date', '<=', $data['date_to']);
        }

        // Фильтрация по числу постов
        if (!empty($data['posts_count_from'])) {
            $builder->where('posts_count', '>=', $data['posts_count_from']);
        }

        if (!empty($data['posts_count_to'])) {
            $builder->where('posts_count', '<=', $data['posts_count_to']);
        }

        // Фильтрация по комментам
        if (!empty($data['comments_count_from'])) {
            $builder->where('comments_count', '>=', $data['comments_count_from']);
        }

        if (!empty($data['comments_count_to'])) {
            $builder->where('comments_count', '<=', $data['comments_count_to']);
        }

        // Фильтрация по реплаям
        if (!empty($data['replies_count_from'])) {
            $builder->where('replies_count', '>=', $data['replies_count_from']);
        }

        if (!empty($data['replies_count_to'])) {
            $builder->where('replies_count', '<=', $data['replies_count_to']);
        }

        // Фильтрация по лайкам
        if (!empty($data['likes_count_from'])) {
            $builder->where('likes_count', '>=', $data['likes_count_from']);
        }

        if (!empty($data['likes_count_to'])) {
            $builder->where('likes_count', '<=', $data['likes_count_to']);
        }

        // Фильтрация по просмотрам
        if (!empty($data['views_count_from'])) {
            $builder->where('views_count', '>=', $data['views_count_from']);
        }

        if (!empty($data['views_count_to'])) {
            $builder->where('views_count', '<=', $data['views_count_to']);
        }

        // Фильтрация по likes/views
        if (!empty($data['likes_views_from'])) {
            $builder->where('likes_views', '>=', $data['likes_views_from']);
        }

        if (!empty($data['likes_views_to'])) {
            $builder->where('likes_views', '<=', $data['likes_views_to']);
        }

        // Фильтрация по likes/comments
        if (!empty($data['likes_comments_from'])) {
            $builder->where('likes_comments', '>=', $data['likes_comments_from']);
        }

        if (!empty($data['likes_comments_to'])) {
            $builder->where('likes_comments', '<=', $data['likes_comments_to']);
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
