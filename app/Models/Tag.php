<?php

namespace App\Models;

use App\Traits\Models\Traits\HasFilter;
use App\Traits\Models\Traits\HasLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Tag extends Model
{
    use HasFactory;
    use HasFilter;

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

}
