<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Profile\ProfileResource;
use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile' => $this->profile
                ? ProfileResource::make($this->profile)->resolve()
                : null,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'roles' => RoleResource::collection($this->roles)->resolve(), // отображаем все роли пользователя
        ];
    }
}
