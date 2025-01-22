<?php

namespace App\Http\Requests\Admin\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

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
            'post' => 'sometimes|array',
            'post.title' => 'required|string|max:255|unique:posts,title,' . $this->route('post')->id,
            'post.description' => 'nullable|string',
            'post.content' => 'nullable|string',
            'post.published_at' => 'nullable|date_format:Y-m-d',
            'post.category_id' => 'nullable|integer|exists:categories,id',
            'post.image' => 'nullable|image',
            'tags' => 'nullable|string',
        ];
    }

    public function passedValidation()
    {
        // Получаем данные 'post' или устанавливаем пустой массив, если 'post' отсутствует или не является массивом
        $postData = $this->input('post', []);

        if (!is_array($postData)) {
            $postData = [];
        }

        // Проверяем, если новое изображение передано
        if ($this->hasFile('post.image')) {
            // Удаляем старое изображение, если оно существует
            if ($this->route('post')->image_path) {
                Storage::disk('public')->delete($this->route('post')->image_path);
            }
            // Мержим данные с путём нового изображения
            $postData = array_merge(
                $postData,
                [
                    'profile_id' => auth()->user()->profile->id,
                    'image_path' => Storage::disk('public')->put('/images', $this->file('post.image')),
                ]
            );
        } else {
            // Если изображение не передано, добавляем только 'profile_id'
            $postData = array_merge(
                $postData,
                [
                    'profile_id' => auth()->user()->profile->id,
                ]
            );
        }

        // Мержим обновленные данные 'post' обратно в запрос
        $this->merge([
            'post' => $postData,
        ]);

        // Обрабатываем теги только если они есть в запросе
        if ($this->has('tags')) {
            if (!empty($this->tags)) {
                // Разделяем теги по запятой, убираем пробелы и пустые значения
                $tags = array_filter(array_map('trim', explode(',', $this->tags)));

                if (!empty($tags)) {
                    $this->merge([
                        'tags' => $tags,
                    ]);
                } else {
                    // Если теги пусты после обработки, устанавливаем пустой массив для удаления всех тегов
                    $this->merge([
                        'tags' => [],
                    ]);
                }
            } else {
                // Если теги переданы, но пусты, устанавливаем пустой массив для удаления всех тегов
                $this->merge([
                    'tags' => [],
                ]);
            }
        }

        // Убедимся, что 'post' всегда присутствует как массив
        if (!$this->has('post')) {
            $this->merge([
                'post' => [],
            ]);
        }
    }

}
