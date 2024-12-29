<?php

namespace App\Http\Filters;

use App\Traits\Models\Traits\HasFilterable;

class ProfileFilter
{
    use HasFilterable;
    protected array $keys = [
        'name',
        'address',
        'phone',
        'avatar',
        'description',
        'gender',
    ];

    protected array $keysRange = [
        'birthed_at' => ['from', 'to'],
    ];

    protected array $keysRelation = [
        'user_name',
        'user_email',
    ];

}
