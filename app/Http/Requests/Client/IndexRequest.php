<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string',
            'content' => 'nullable|string',
            'published_at_from' => 'nullable|date_format:Y-m-d',
            'published_at_to' => 'nullable|date_format:Y-m-d',
            'views_from' => 'nullable|integer',
            'views_to' => 'nullable|integer',
            'category_title' => 'nullable|string',
            'profile_name' => 'nullable|string',
            'tags_title' => 'nullable|string',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ];
    }

    public function passedValidation()
    {
       return $this->merge([
           'page' => $this->page ?? 1,
           'per_page' => $this->per_page ?? 9,
       ]);
    }
}
