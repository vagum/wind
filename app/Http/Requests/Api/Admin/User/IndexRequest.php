<?php

namespace App\Http\Requests\Api\Admin\User;

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
            'name' => 'nullable|string',
            'email' => 'nullable|string',
            'email_verified_at_from' => 'nullable|string',
            'email_verified_at_to' => 'nullable|string',
        ];
    }
}
