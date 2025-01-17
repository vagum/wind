<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;
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
            'name' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'avatar' => 'nullable|string',
            'description' => 'nullable|string',
            'gender' => 'nullable|string',
            'birthed_at' => 'nullable|date_format:Y-m-d',
            'image' => 'nullable|image',
        ];
    }

    public function passedValidation()
    {
        if ($this->hasFile('image')) {
            $this->merge([
                'avatar' => Storage::disk('public')->put('/images', $this->image),
            ]);

        }
    }
}
