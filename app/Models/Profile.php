<?php

namespace App\Models;

use App\Traits\LogChanges;
use App\Traits\Models\Traits\HasFilter;
use App\Traits\Models\Traits\HasLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory, LogChanges;
    use HasFilter;

//    public function likedPosts(): BelongsToMany
//    {
//        return $this->belongsToMany(Post::class, 'post_profile_likes');
//    }

    public function likedPosts()
    {
        return $this->morphedByMany(Post::class, 'likeable');
}

    public function viewedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_profile_views');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->avatar);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

}
