<?php

namespace Tests\Unit\Requests\Mcu\Master;

use App\Http\Requests\Mcu\Master\StoreMcuAnamnesisRequest;
use App\Http\Requests\Mcu\Master\StoreMcuLabRequest;
use App\Http\Requests\Mcu\Master\StoreMcuMedicalActionRequest;
use App\Http\Requests\Mcu\Master\StoreMcuPhysicalExamRequest;
use App\Http\Requests\Mcu\Master\StoreMcuRadiologyRequest;
use App\Http\Requests\Mcu\Master\UpdateMcuLabRequest;
use App\Models\McuLab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class MasterRequestsTest extends TestCase
{
    use RefreshDatabase;

    protected function assertStoreRulesContain(string $requestClass, array $expectedKeys): void
    {
        $rules = (new $requestClass())->rules();
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $rules, "Missing key $key in $requestClass");
        }
    }

    public function test_store_mcu_lab_rules(): void
    {
        $this->assertStoreRulesContain(StoreMcuLabRequest::class, ['code','name','category','price','display_order','status']);
        $rules = (new StoreMcuLabRequest())->rules();
        $this->assertStringContainsString('unique:mcu_labs', $rules['code']);
        $this->assertStringContainsString('required', $rules['code']);
    }

    public function test_store_mcu_radiology_rules(): void
    {
        $this->assertStoreRulesContain(StoreMcuRadiologyRequest::class, ['code','name','price','status']);
    }

    public function test_store_mcu_anamnesis_rules(): void
    {
        $this->assertStoreRulesContain(StoreMcuAnamnesisRequest::class, ['code','name','price','status']);
    }

    public function test_store_mcu_physical_rules(): void
    {
        $this->assertStoreRulesContain(StoreMcuPhysicalExamRequest::class, ['code','name','price','status']);
    }

    public function test_store_mcu_medical_action_rules(): void
    {
        $this->assertStoreRulesContain(StoreMcuMedicalActionRequest::class, ['code','name','price','status']);
    }

    public function test_update_mcu_lab_unique_ignores_self(): void
    {
        $lab = McuLab::create(['code' => 'LAB-001', 'name' => 'Lab', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        $request = new UpdateMcuLabRequest();
        $request->setRouteResolver(fn() => new class($lab) {
            public function __construct(public $lab) {}
            public function parameter($key) { return $this->lab; }
        });
        $rules = $request->rules();
        $this->assertIsArray($rules['code']);
        // Should contain Rule object
        $this->assertCount(4, $rules['code']);
    }

    public function test_store_validation_passes_with_valid_data(): void
    {
        $data = ['code' => 'LAB-TEST', 'name' => 'Test Lab', 'price' => 25000, 'display_order' => 1, 'status' => true];
        $validator = Validator::make($data, (new StoreMcuLabRequest())->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_store_validation_fails_when_code_missing(): void
    {
        $validator = Validator::make(['name' => 'Test', 'price' => 10000, 'display_order' => 1, 'status' => true], (new StoreMcuLabRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('code', $validator->errors()->toArray());
    }

    public function test_store_validation_fails_when_price_negative(): void
    {
        $validator = Validator::make(['code' => 'LAB-NEG', 'name' => 'Test', 'price' => -10, 'display_order' => 1, 'status' => true], (new StoreMcuLabRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
    }

    public function test_store_validation_fails_when_display_order_missing(): void
    {
        $validator = Validator::make(['code' => 'LAB-NODISP', 'name' => 'Test', 'price' => 10000, 'status' => true], (new StoreMcuLabRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('display_order', $validator->errors()->toArray());
    }

    public function test_store_validation_fails_when_status_invalid(): void
    {
        $validator = Validator::make(['code' => 'LAB-STAT', 'name' => 'Test', 'price' => 10000, 'display_order' => 1, 'status' => 'invalid'], (new StoreMcuLabRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_authorize_returns_true_when_authenticated(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $this->assertTrue((new StoreMcuLabRequest())->authorize());
    }

    public function test_authorize_false_when_guest(): void
    {
        $this->assertFalse((new StoreMcuLabRequest())->authorize());
    }
}
