<?php

namespace App\Models;

use App\Traits\Models\Traits\HasFilter;
use App\Traits\Models\Traits\HasLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function category(): BelongsTo
    {
        return $this->post->category();
    }


    public function tags(): BelongsToMany
    {
        return $this->post->tags();
    }

    public function user(): BelongsTo
    {
        return $this->profile->user();
    }

    public function likedProfiles()
    {
        return $this->morphToMany(Profile::class,'likeable');

    }

    /**
     * Связь для ответов (реплаев) к этому комментарию.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('replies', 'profile');
    }

    /**
     * Связь для родительского комментария.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function getIsLikedAttribute(): bool
    {
        // Проверяем, аутентифицирован ли пользователь
        if (!auth()->check()) {
            return false;
        }

        // Получаем профиль пользователя, если он существует
        $profile = auth()->user()->profile;

        // Если профиль не найден, возвращаем false
        if (!$profile) {
            return false;
        }

        // Проверяем, содержит ли коллекция likedProfiles профиль текущего пользователя
        return $this->likedProfiles->contains($profile->id);
    }

    public function getCommentsCountAttribute(): int
    {
        return $this->post->comments->count();
    }

}
