<?php

namespace App\Http\Requests\Mcu\Registration;

use Illuminate\Foundation\Http\FormRequest;

class StoreMcuRegistrationRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'mcu_package_id' => 'required|exists:mcu_packages,id',
            'registration_date' => 'required|date',
        ];
    }
}
