<?php

namespace App\Models;

use App\Observers\PostObserver;
use App\Traits\Models\Traits\HasFilter;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[ObservedBy(PostObserver::class)]
class Post extends Model
{
//    protected $guarded = false;

    use HasFactory;
    use HasFilter;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

//    public function likedProfiles(): BelongsToMany
//    {
//        return $this->belongsToMany(Profile::class, 'post_profile_likes');
//    }

    public function likedProfiles()
    {
        return $this->morphToMany(Profile::class,'likeable')->withTimestamps();

    }

    public function viewedProfiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'post_profile_views');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function user(): BelongsTo
    {
        return $this->profile->user();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        // Если image_path уже содержит 'http', возвращаем его как есть.
        if (Str::contains($this->image_path, 'http')) {
            return $this->image_path;
        }

        // В противном случае генерируем URL через дисковое хранилище.
        return Storage::disk('public')->url($this->image_path);
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
        return $this->comments()->whereNull('parent_id')->count();
    }

    public function getViewsCountAttribute(): int
    {
        return DB::table('post_profile_views')
            ->where('post_id', $this->id)
            ->count();
    }

}
