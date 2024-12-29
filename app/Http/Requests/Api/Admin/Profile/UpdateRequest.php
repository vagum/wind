<?php

namespace App\Http\Requests\Api\Admin\Profile;

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
            'user_id' => 'required|integer|exists:users,id',
            'login' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'avatar' => 'nullable|string',
            'description' => 'nullable|string',
            'gender' => 'nullable|string',
            'birthed_at' => 'nullable|date_format:Y-m-d',
        ];
    }
}
