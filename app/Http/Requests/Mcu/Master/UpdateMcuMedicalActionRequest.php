<?php

namespace App\Http\Requests\Mcu\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMcuMedicalActionRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        $id = $this->route('mcu_medical_action')?->id ?? $this->route('mcuMedicalAction')?->id ?? $this->route('mcu_medical_action');
        return [
            'code' => ['required','string','max:255', Rule::unique('mcu_medical_actions','code')->ignore($id)],
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ];
    }
}
