<?php

namespace App\Http\Requests\Mcu\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMcuAnamnesisRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        $id = $this->route('mcu_anamnesis')?->id ?? $this->route('mcuAnamnesis')?->id ?? $this->route('mcu_anamnesis');
        return [
            'code' => ['required','string','max:255', Rule::unique('mcu_anamneses','code')->ignore($id)],
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ];
    }
}
