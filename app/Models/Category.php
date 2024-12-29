<?php

namespace App\Models;

use App\Traits\LogChanges;
use App\Traits\Models\Traits\HasFilter;
use App\Traits\Models\Traits\HasLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Category extends Model
{
    use HasFactory, LogChanges;
    use HasFilter, HasLog;

    protected static function boot()
    {
        parent::boot();

        // Пробуем добавить/переписать логирование события создания
        static::created(function ($category) {
            $category->logChanges('CategoryBoot@created');
        });

    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasManyThrough
    {
        return $this->hasManyThrough(Comment::class, Post::class);
    }

    public function comment(): HasOneThrough
    {
        return $this->hasOneThrough(Comment::class, Post::class);
    }
}
