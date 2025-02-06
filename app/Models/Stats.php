<?php

namespace App\Models;

use App\Traits\Models\Traits\Stats\HasFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stats extends Model
{
    /** @use HasFactory<\Database\Factories\StatsFactory> */
    use HasFactory;
    use HasFilter;

    protected $casts = [
        'date' => 'datetime',
    ];

}
