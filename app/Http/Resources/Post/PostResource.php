<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Profile\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'description' => $this->description,
            'published_at' => $this->published_at,
            'views' => $this->views_count, // Используем getViewsCountAttribute в модели Post.php
            'likes' => $this->liked_profiles_count, // Подсчет лайков,
            'category_title' => CategoryResource::make($this->category)->resolve()['title'],
            'profile_name' => ProfileResource::make($this->profile)->resolve(),
            'tags_title' => $this->tags->pluck('title'),
            'image_url' => $this->image_url,
            'is_liked' => (bool) $this->is_liked,
            'comments_count' => $this->parent_comments_count,
//            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
