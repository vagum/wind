<?php

namespace App\Http\Filters;

use App\Traits\Models\Traits\HasFilterable;

class CommentFilter
{
    use HasFilterable;
    /**
     * Create a new class instance.
     */
    protected array $keys = [
        'content',
    ];
}
