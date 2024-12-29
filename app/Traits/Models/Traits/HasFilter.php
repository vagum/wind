<?php

namespace App\Traits\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFilter
{
    public function scopeFilter(Builder $builder, array $data, ): Builder
    {
        $ClassName = 'App\\Http\\Filters\\'.class_basename($this).'Filter';

        // Проверка на существование вызываемого класса
        if (class_exists($ClassName)) {
            return (new $ClassName())->apply($data, $builder);
        }
        // Возвращаем builder без применения фильтра если класса нет
        return $builder;
    }
}
