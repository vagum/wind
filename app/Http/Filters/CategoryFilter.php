<?php

namespace App\Http\Filters;

use App\Traits\Models\Traits\HasFilterable;

class CategoryFilter
{
    use HasFilterable;
    protected array $keys = [
        'title',
    ];
}