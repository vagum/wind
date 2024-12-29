<?php

namespace App\Http\Requests\Api\Admin\Comment;

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
            'author' => 'required|string',
            'content' => 'required|string',
            'post_id' => 'required|integer',
            'parent_id' => 'integer',
            'likes' => 'integer',
        ];
    }
}
