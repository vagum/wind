<?php

namespace App\Http\Filters;

use App\Traits\Models\Traits\HasFilterable;
use Illuminate\Database\Eloquent\Builder;

class PostFilter
{
    use HasFilterable;

    protected array $keys = [
        'title',
        'content',
        'image_path',
        'description',

    ];

    protected array $keysRange = [
        'published_at' => ['from', 'to'],
        'views' => ['from', 'to'],
    ];

    protected array $keysRelation = [
        'category_title',
        'profile_name',
        'tags_title',
    ];

}


