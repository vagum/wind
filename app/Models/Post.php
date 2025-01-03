<?php

namespace App\Models;

use App\Http\Filters\PostFilter;
use App\Observers\PostObserver;
use App\Traits\LogChanges;
use App\Traits\Models\Traits\HasFilter;
use App\Traits\Models\Traits\HasLog;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        return $this->morphToMany(Profile::class,'likeable');

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

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
