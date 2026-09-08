<?php

namespace App\Http\Requests\Mcu\Examination;

use Illuminate\Foundation\Http\FormRequest;

class StoreRadiologyRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        return [
            'results' => 'nullable|array',
            'results.*' => 'nullable|string',
            'is_normal' => 'nullable|array',
            'is_normal.*' => 'nullable|boolean',
        ];
    }
}
