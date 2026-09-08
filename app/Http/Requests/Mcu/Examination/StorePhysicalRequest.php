<?php

namespace App\Http\Requests\Mcu\Examination;

use Illuminate\Foundation\Http\FormRequest;

class StorePhysicalRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        return [
            'vital_signs' => 'nullable|array',
            'head_and_neck' => 'nullable|array',
            'thorax' => 'nullable|array',
            'abdomen' => 'nullable|array',
            'urogenital' => 'nullable|array',
            'extremities' => 'nullable|array',
            'others' => 'nullable|array',
        ];
    }
}
