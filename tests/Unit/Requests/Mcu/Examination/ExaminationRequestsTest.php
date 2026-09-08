<?php

namespace Tests\Unit\Requests\Mcu\Examination;

use App\Http\Requests\Mcu\Examination\StoreAnamnesisRequest;
use App\Http\Requests\Mcu\Examination\StoreDoctorRequest;
use App\Http\Requests\Mcu\Examination\StoreLabRequest;
use App\Http\Requests\Mcu\Examination\StorePhysicalRequest;
use App\Http\Requests\Mcu\Examination\StoreRadiologyRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ExaminationRequestsTest extends TestCase
{
    use RefreshDatabase;
    public function test_lab_rules(): void
    {
        $rules = (new StoreLabRequest())->rules();
        $this->assertArrayHasKey('results', $rules);
        $this->assertEquals('nullable|array', $rules['results']);
        $this->assertEquals('nullable|string', $rules['results.*']);
    }

    public function test_radiology_rules(): void
    {
        $rules = (new StoreRadiologyRequest())->rules();
        $this->assertArrayHasKey('results', $rules);
        $this->assertArrayHasKey('is_normal', $rules);
        $this->assertArrayHasKey('is_normal.*', $rules);
    }

    public function test_anamnesis_rules(): void
    {
        $rules = (new StoreAnamnesisRequest())->rules();
        $this->assertArrayHasKey('results', $rules);
    }

    public function test_physical_rules(): void
    {
        $rules = (new StorePhysicalRequest())->rules();
        $this->assertArrayHasKey('vital_signs', $rules);
        $this->assertArrayHasKey('head_and_neck', $rules);
        $this->assertArrayHasKey('thorax', $rules);
        $this->assertArrayHasKey('abdomen', $rules);
        $this->assertArrayHasKey('urogenital', $rules);
        $this->assertArrayHasKey('extremities', $rules);
        $this->assertArrayHasKey('others', $rules);
        foreach ($rules as $field => $rule) {
            $this->assertEquals('nullable|array', $rule);
        }
    }

    public function test_doctor_rules(): void
    {
        $rules = (new StoreDoctorRequest())->rules();
        $this->assertArrayHasKey('results', $rules);
    }

    public function test_lab_validation_passes_with_valid(): void
    {
        $validator = Validator::make(['results' => [1 => '110 mg/dL']], (new StoreLabRequest())->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_lab_validation_passes_with_empty(): void
    {
        $validator = Validator::make([], (new StoreLabRequest())->rules());
        $this->assertTrue($validator->passes());
        $validator = Validator::make(['results' => []], (new StoreLabRequest())->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_physical_validation_passes_with_valid(): void
    {
        $data = ['vital_signs' => ['height' => '170'], 'head_and_neck' => ['head' => 'Normal']];
        $validator = Validator::make($data, (new StorePhysicalRequest())->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_authorize(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $this->assertTrue((new StoreLabRequest())->authorize());
        $this->assertTrue((new StoreRadiologyRequest())->authorize());
        $this->assertTrue((new StoreAnamnesisRequest())->authorize());
        $this->assertTrue((new StorePhysicalRequest())->authorize());
        $this->assertTrue((new StoreDoctorRequest())->authorize());
    }
}
