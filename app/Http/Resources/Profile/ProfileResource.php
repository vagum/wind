<?php

namespace App\Http\Resources\Profile;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        $userData = UserResource::make($this->user)->resolve();

        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'name'        => $this->name,
            'address'     => $this->address,
            'phone'       => $this->phone,
            'avatar'      => $this->avatar,
            'description' => $this->description,
            'gender'      => $this->gender,
            'birthed_at'  => $this->birthed_at,
            'user'        => $this->whenLoaded('user', function () {
                return [
                    'id'   => $this->user->id,
                    'name' => $this->user->name,
                    // Другие поля пользователя, если необходимо
                ];
            }),
            'image_url'   => $this->image_url,
        ];
    }
}
