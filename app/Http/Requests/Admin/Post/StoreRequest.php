<?php

namespace App\Http\Requests\Admin\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:posts,title',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'published_at' => 'nullable|date_format:Y-m-d',
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'nullable|image',
            'image_path'  => 'string|nullable', // без этого не провалидирует повторно
        ];
    }

    public function passedValidation()
    {
        if ($this->hasFile('image')) {
            $this->merge([
                'image_path' => Storage::disk('public')->put('/images', $this->image),
            ]);

        }
    }
}
