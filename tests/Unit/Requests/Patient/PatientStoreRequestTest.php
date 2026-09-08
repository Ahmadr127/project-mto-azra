<?php

namespace Tests\Unit\Requests\Patient;

use App\Http\Requests\Patient\PatientStoreRequest;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PatientStoreRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function rules(): array
    {
        return (new PatientStoreRequest())->rules();
    }

    public function test_authorize_returns_true_when_authenticated(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $request = new PatientStoreRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_authorize_returns_false_when_guest(): void
    {
        $request = new PatientStoreRequest();
        // Without auth, should be false
        $this->assertFalse($request->authorize());
    }

    public function test_rules_contain_required_fields(): void
    {
        $rules = $this->rules();
        $this->assertArrayHasKey('patient_code', $rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertStringContainsString('required', $rules['patient_code']);
        $this->assertStringContainsString('unique:patients', $rules['patient_code']);
        $this->assertStringContainsString('required', $rules['name']);
    }

    public function test_validation_passes_with_valid_data(): void
    {
        $data = [
            'patient_code' => 'RM-001',
            'nik' => '3271000000000001',
            'name' => 'Test Pasien',
            'gender' => 'L',
            'birth_place' => 'Bogor',
            'birth_date' => '1990-05-15',
            'address' => 'Jl Test',
            'kelurahan' => 'Kel A',
            'kecamatan' => 'Kec B',
            'kabupaten_kota' => 'Kota Bogor',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '16111',
            'phone' => '08123456789',
            'emergency_phone' => '081987654321',
            'department' => 'IT',
            'employee_status' => 'Tetap',
            'bpjs' => '1234567890',
        ];

        $validator = Validator::make($data, $this->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_validation_fails_when_required_fields_missing(): void
    {
        $validator = Validator::make([], $this->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('patient_code', $validator->errors()->toArray());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_validation_fails_when_patient_code_not_unique(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'Existing']);

        $data = ['patient_code' => 'RM-001', 'name' => 'New'];
        $validator = Validator::make($data, $this->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('patient_code', $validator->errors()->toArray());
    }

    public function test_validation_fails_when_nik_too_long(): void
    {
        $data = ['patient_code' => 'RM-002', 'name' => 'Test', 'nik' => str_repeat('1', 17)];
        $validator = Validator::make($data, $this->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('nik', $validator->errors()->toArray());
    }

    public function test_validation_fails_when_gender_invalid(): void
    {
        $data = ['patient_code' => 'RM-003', 'name' => 'Test', 'gender' => 'X'];
        $validator = Validator::make($data, $this->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('gender', $validator->errors()->toArray());
    }

    public function test_validation_fails_when_birth_date_future(): void
    {
        $future = now()->addDay()->format('Y-m-d');
        $data = ['patient_code' => 'RM-004', 'name' => 'Test', 'birth_date' => $future];
        $validator = Validator::make($data, $this->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('birth_date', $validator->errors()->toArray());
    }

    public function test_validation_fails_when_kode_pos_too_long(): void
    {
        $data = ['patient_code' => 'RM-005', 'name' => 'Test', 'kode_pos' => str_repeat('1', 11)];
        $validator = Validator::make($data, $this->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('kode_pos', $validator->errors()->toArray());
    }

    public function test_messages_custom(): void
    {
        $request = new PatientStoreRequest();
        $messages = $request->messages();
        $this->assertArrayHasKey('patient_code.required', $messages);
        $this->assertArrayHasKey('patient_code.unique', $messages);
        $this->assertArrayHasKey('name.required', $messages);
    }
}
