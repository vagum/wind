<?php

namespace App\Http\Filters;

use App\Traits\Models\Traits\HasFilterable;

class TagFilter
{
    use HasFilterable;
    protected array $keys = [
        'title',
    ];
}
