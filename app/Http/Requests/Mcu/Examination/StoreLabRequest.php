<?php

namespace App\Http\Requests\Mcu\Examination;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        return [
            'results' => 'nullable|array',
            'results.*' => 'nullable|string',
        ];
    }
}
