<?php

namespace App\Http\Resources\Comment;

use App\Http\Resources\Profile\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'content'        => $this->content,
            'post_id'        => $this->post_id,
            'parent_id'      => $this->parent_id,
            // Используем флаг, полученный через withExists
            'is_liked'       => (bool) $this->is_liked,
            // Количество лайков – через withCount
            'likes'          => $this->liked_profiles_count,
            'created_at'     => $this->created_at->toDateTimeString(),
            // Если для дочерних комментариев используется отношение replies:
            'comments_count' => $this->replies_count,
            'replies_count'  => $this->when(isset($this->replies_count), $this->replies_count),
            'replies'        => CommentResource::collection($this->whenLoaded('replies')),
            'profile'        => new ProfileResource($this->whenLoaded('profile')),
        ];
    }
}
