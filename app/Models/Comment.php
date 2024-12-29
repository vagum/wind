<?php

namespace App\Models;

use App\Traits\Models\Traits\HasFilter;
use App\Traits\Models\Traits\HasLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
