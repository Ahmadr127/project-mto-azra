<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class PatientStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'patient_code' => 'required|string|unique:patients,patient_code',
            'nik' => 'nullable|string|max:16',
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'address' => 'nullable|string|max:1000',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'emergency_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'department' => 'nullable|string|max:255',
            'employee_status' => 'nullable|string|max:255',
            'bpjs' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_code.required' => 'No. RM wajib diisi.',
            'patient_code.unique' => 'No. RM sudah terdaftar.',
            'name.required' => 'Nama pasien wajib diisi.',
            'birth_date.before_or_equal' => 'Tanggal lahir tidak boleh di masa depan.',
            'photo.image' => 'Foto harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
