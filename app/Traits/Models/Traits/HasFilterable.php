<?php

namespace App\Traits\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFilterable
{
    public function apply(array $data, Builder $builder): Builder
    {
        // Применяем фильтрацию для текстовых полей
        $this->applyTextFilter($builder, $data, $this->keys ?? []);

        // Применяем фильтрацию для диапазонов
        $this->applyRangeFilter($builder, $data, $this->keysRange ?? []);

        // Применяем фильтрацию для полей, связанных с другими моделями
        $this->applyRelationFilter($builder, $data, $this->keysRelation ?? []);

        return $builder;
    }

    protected function applyTextFilter(Builder $builder, array $data, array $keys): void
    {
        foreach ($keys as $key) {
            if (!empty($data[$key])) {
                $builder->where($key, 'ilike', "%$data[$key]%");
            }
        }
    }

    protected function applyRangeFilter(Builder $builder, array $data, array $keysRange): void
    {
        foreach ($keysRange as $key => $ranges) {
            foreach ($ranges as $range) {
                $field = "{$key}_$range";
                if (!empty($data[$field])) {
                    $operator = $range === 'from' ? '>' : '<';
                    $builder->where($key, $operator, $data[$field]);
                }
            }
        }
    }

    protected function applyRelationFilter(Builder $builder, array $data, array $keysRelation): void
    {
        foreach ($keysRelation as $key) {
            // Проверяем, есть ли поле в данных для фильтрации
            if (!empty($data[$key])) {
                // Извлекаем имя отношения, удаляя суффикс (например, '_title')
                $relation = substr($key, 0, strpos($key, '_')) ?: $key;

                // Извлекаем поле для фильтрации (например, 'title' из 'category_title')
                $field = substr($key, strpos($key, '_') + 1);

                // Если отношение существует в модели, применяем фильтрацию
                if (method_exists($builder->getModel(), $relation)) {
                    // Если поле указано, фильтруем его, иначе фильтруем по первичному полю (например, 'title')
                    $builder->whereRelation($relation, $field, 'ilike', "%$data[$key]%");
                }
            }
        }
    }

}
