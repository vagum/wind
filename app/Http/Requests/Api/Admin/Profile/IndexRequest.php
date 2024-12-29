<?php

namespace App\Http\Requests\Api\Admin\Profile;

use App\Traits\Models\Traits\HasFilterable;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    use HasFilterable;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'avatar' => 'nullable|string',
            'description' => 'nullable|string',
            'gender' => 'nullable|string',
            'birthed_at_from' => 'nullable|date_format:Y-m-d',
            'birthed_at_to' => 'nullable|date_format:Y-m-d',
            'user_name' => 'nullable|string',
            'user_email' => 'nullable|string',
        ];
    }
}
