<?php

namespace App\Http\Resources\Stats;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatsResource extends JsonResource
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
            'date' => $this->date->format('d.m.Y'),
            'posts_count' => $this->posts_count,
            'comments_count' => $this->comments_count,
            'replies_count' => $this->replies_count,
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'likes_views' => number_format($this->likes_views,2),
            'likes_comments' => number_format($this->likes_comments,2),
        ];
    }

}
