<?php

namespace App\Http\Filters;

use App\Traits\Models\Traits\HasFilterable;

class UserFilter
{
    use HasFilterable;
    protected array $keys = [
        'name',
        'email',
    ];

    protected array $keysRange = [
        'email_verified_at' => ['from', 'to'],
    ];
}
