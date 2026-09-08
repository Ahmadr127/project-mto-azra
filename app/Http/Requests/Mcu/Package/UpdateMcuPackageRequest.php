<?php

namespace App\Http\Requests\Mcu\Package;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMcuPackageRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        $id = $this->route('mcu_package')?->id ?? $this->route('mcuPackage')?->id ?? $this->route('mcu_package');
        return [
            'code' => ['required','string','max:255', Rule::unique('mcu_packages','code')->ignore($id)],
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
