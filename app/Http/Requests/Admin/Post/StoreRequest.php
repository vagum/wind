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
            'post.title' => 'required|string|max:255|unique:posts,title',
            'post.description' => 'nullable|string',
            'post.content' => 'nullable|string',
            'post.published_at' => 'nullable|date_format:Y-m-d',
            'post.category_id' => 'required|integer|exists:categories,id',
            'post.image' => 'nullable|image',
            'tags' => 'nullable|string',
        ];
    }

    public function passedValidation()
    {
        isset($this->post['image']) ? $this->merge([
             // добавляем то что внутри в post, чтобы выровнять с тагами
             'post' => [
                 // обрезаем post. , чтобы добавить profile_id и image_path на тот же уровень
                 ...$this->validated()['post'],
                 // получаем профайл залогиненного пользователя
                 'profile_id' => auth()->user()->profile->id,
                 // добавляем image_path когда картинка есть
                 'image_path' => Storage::disk('public')->put('/images', $this->post['image'])
             ],
            // добавляем преобразование строки тагов в массив, иначе просто строка будет
            'tags' => explode(',', $this->tags),
        ]) : $this->merge([
            // добавляем то что внутри в post, чтобы выровнять с тагами
            'post' => [
                // обрезаем post. , чтобы добавить profile_id на тот же уровень
                ...$this->validated()['post'],
                // получаем профайл залогиненного пользователя
                'profile_id' => auth()->user()->profile->id,
            ],
            // если картинки нет, то добавляем только преобразование строки тагов в массив
            'tags' => explode(',', $this->tags),
        ]);
    }

}
