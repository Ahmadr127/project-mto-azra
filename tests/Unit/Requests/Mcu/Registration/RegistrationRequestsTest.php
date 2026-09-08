<?php

namespace Tests\Unit\Requests\Mcu\Registration;

use App\Http\Requests\Mcu\Registration\StoreMcuRegistrationRequest;
use App\Http\Requests\Mcu\Registration\UpdateMcuRegistrationRequest;
use App\Models\McuPackage;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RegistrationRequestsTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_rules(): void
    {
        $rules = (new StoreMcuRegistrationRequest())->rules();
        $this->assertArrayHasKey('patient_id', $rules);
        $this->assertArrayHasKey('mcu_package_id', $rules);
        $this->assertArrayHasKey('registration_date', $rules);
        $this->assertStringContainsString('exists:patients', $rules['patient_id']);
        $this->assertStringContainsString('exists:mcu_packages', $rules['mcu_package_id']);
    }

    public function test_update_rules_contains_status(): void
    {
        $rules = (new UpdateMcuRegistrationRequest())->rules();
        $this->assertArrayHasKey('status', $rules);
        $this->assertStringContainsString('in:registered', $rules['status']);
    }

    public function test_store_validation_passes(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Test']);
        $package = McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $data = ['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now()->format('Y-m-d')];
        $validator = Validator::make($data, (new StoreMcuRegistrationRequest())->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_store_validation_fails_missing(): void
    {
        $validator = Validator::make([], (new StoreMcuRegistrationRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('patient_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('mcu_package_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('registration_date', $validator->errors()->toArray());
    }

    public function test_store_validation_fails_invalid_ids(): void
    {
        $validator = Validator::make(['patient_id' => 99999, 'mcu_package_id' => 99999, 'registration_date' => now()->format('Y-m-d')], (new StoreMcuRegistrationRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('patient_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('mcu_package_id', $validator->errors()->toArray());
    }

    public function test_store_validation_fails_invalid_date(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Test']);
        $package = McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $validator = Validator::make(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => 'not-a-date'], (new StoreMcuRegistrationRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('registration_date', $validator->errors()->toArray());
    }

    public function test_update_validation_fails_invalid_status(): void
    {
        $validator = Validator::make(['patient_id' => 1, 'mcu_package_id' => 1, 'registration_date' => now()->format('Y-m-d'), 'status' => 'invalid'], (new UpdateMcuRegistrationRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_authorize(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $this->assertTrue((new StoreMcuRegistrationRequest())->authorize());
        $this->assertTrue((new UpdateMcuRegistrationRequest())->authorize());
    }
}
