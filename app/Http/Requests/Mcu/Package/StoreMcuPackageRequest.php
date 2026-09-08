<?php

namespace App\Http\Requests\Mcu\Package;

use Illuminate\Foundation\Http\FormRequest;

class StoreMcuPackageRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:255|unique:mcu_packages,code',
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'items' => 'nullable|array',
            'items.*' => 'string',
        ];
    }
}
