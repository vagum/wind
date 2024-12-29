<?php

namespace App\Http\Filters;

use App\Traits\Models\Traits\HasFilterable;

class RoleFilter
{
    use HasFilterable;
    protected array $keys = [
        'title',
    ];
}
