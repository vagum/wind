<?php

namespace App\Models;

use App\Traits\Models\Traits\HasFilter;
use App\Traits\Models\Traits\HasLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    use HasFilter;
    protected $guarded = false;

    const ROLE_ADMIN = 1;
    const ROLE_TRADER = 2;
    const ROLE_CONTENT = 3;

    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'admin',
            self::ROLE_TRADER => 'trader',
            self::ROLE_CONTENT => 'content',
        ];
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
