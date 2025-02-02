<?php

namespace App\Http\Resources\Comment;

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
            'id' => $this->id,
            'content' => $this->content,
            'post_id' => $this->post_id,
            'parent_id' => $this->parent_id,
            'is_liked' => $this->is_liked, // getIsLikedAttribute в Models/Comment.php
            'likes' => $this->likedProfiles()->count(), // Подсчет лайков,
            'created_at' => $this->created_at->toDateTimeString(),
            'comments_count' => $this->comments_count, // getCommentsCountAttribute в Models/Comment.php
            'replies_count'  => $this->when(isset($this->replies_count), $this->replies_count),
            'replies'        => CommentResource::collection($this->whenLoaded('replies')),
            'profile' => [
                'id' => $this->profile->id,
                'name' => $this->profile->name,
            ],
        ];
    }
}
