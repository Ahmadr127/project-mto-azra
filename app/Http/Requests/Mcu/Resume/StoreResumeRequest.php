<?php

namespace App\Http\Requests\Mcu\Resume;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        return [
            'conclusion' => 'nullable|string',
            'recommendation' => 'nullable|string',
        ];
    }
}
