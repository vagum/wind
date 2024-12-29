<?php

namespace App\Http\Requests\Api\Admin\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'author' => 'nullable|string',
            'title' => 'nullable|string|max:255|unique:posts,title,' . $this->post->id,
            'category' => 'nullable|string',
            'tag' => 'nullable|string',
            'image_path' => 'nullable|string',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'published_at' => 'nullable|date_format:Y-m-d',
//            'likes' => 'nullable|integer',
//            'views' => 'nullable|integer',
            'status' => 'nullable|integer',
            'is_published' => 'nullable|boolean',
        ];
    }
}
