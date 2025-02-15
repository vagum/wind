<?php

namespace App\Models;

use App\Traits\Models\Traits\HasFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;
    use HasFilter;

    protected $guarded = false;

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    // Удаляем или закомментируем эти методы,
    // чтобы не было вызова $this->post->category() и $this->post->tags()
    /*
    public function category(): BelongsTo
    {
        return $this->post->category();
    }

    public function tags(): BelongsToMany
    {
        return $this->post->tags();
    }
    */

    public function user(): BelongsTo
    {
        return $this->profile->user();
    }

    public function likedProfiles()
    {
        return $this->morphToMany(Profile::class, 'likeable')->withTimestamps();
    }

    /**
     * Связь для родительского комментария.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Рекурсивное отношение для загрузки ответов (дочерних комментариев).
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function getIsLikedAttribute(): bool
    {
        // Если вы всё-таки хотите акссесор,
        // берите значение только из предзагруженного атрибута:
        return (bool) ($this->attributes['is_liked'] ?? false);
    }

    public function getCommentsCountAttribute(): int
    {
        return $this->replies_count ?? $this->replies->count();
    }

    public function getCategoryAttribute()
    {
        return $this->post->category;
    }

    public function getTagsAttribute()
    {
        return $this->post->tags;
    }
}
