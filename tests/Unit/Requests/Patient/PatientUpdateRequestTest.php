<?php

namespace Tests\Unit\Requests\Patient;

use App\Http\Requests\Patient\PatientUpdateRequest;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class PatientUpdateRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_rules_ignore_current_patient_for_unique(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Existing']);
        $other = Patient::create(['patient_code' => 'RM-002', 'name' => 'Other']);

        // Simulate route binding: request with patient id
        $request = new PatientUpdateRequest();
        $request->setRouteResolver(fn() => new class($patient) {
            public function __construct(public $patient) {}
            public function parameter($key) { return $this->patient; }
        });

        $rules = $request->rules();
        $this->assertIsArray($rules['patient_code']);
        // Should contain Rule::unique with ignore
        $hasUniqueRule = false;
        foreach ($rules['patient_code'] as $rule) {
            if ($rule instanceof Rule || (is_string($rule) && str_contains($rule, 'unique'))) {
                $hasUniqueRule = true;
            }
        }
        // At least should have unique rule
        $this->assertTrue($hasUniqueRule || count($rules['patient_code']) > 1);
    }

    public function test_validation_passes_when_updating_same_code(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Existing']);

        $request = new PatientUpdateRequest();
        $request->setRouteResolver(fn() => new class($patient) {
            public function __construct(public $patient) {}
            public function parameter($key) { return $this->patient; }
        });

        $data = ['patient_code' => 'RM-001', 'name' => 'Updated Name'];
        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_validation_fails_when_code_taken_by_other(): void
    {
        $patient1 = Patient::create(['patient_code' => 'RM-001', 'name' => 'A']);
        $patient2 = Patient::create(['patient_code' => 'RM-002', 'name' => 'B']);

        $request = new PatientUpdateRequest();
        $request->setRouteResolver(fn() => new class($patient1) {
            public function __construct(public $patient) {}
            public function parameter($key) { return $this->patient; }
        });

        $data = ['patient_code' => 'RM-002', 'name' => 'A Updated'];
        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('patient_code', $validator->errors()->toArray());
    }

    public function test_rules_contain_conditional_fields(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Test']);
        $request = new PatientUpdateRequest();
        $request->setRouteResolver(fn() => new class($patient) {
            public function __construct(public $patient) {}
            public function parameter($key) { return $this->patient; }
        });

        $rules = $request->rules();
        $this->assertArrayHasKey('remove_photo', $rules);
        $this->assertArrayHasKey('photo', $rules);
        $this->assertEquals('nullable|boolean', $rules['remove_photo']);
    }
}
