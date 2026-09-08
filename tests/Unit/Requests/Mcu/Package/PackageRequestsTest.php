<?php

namespace Tests\Unit\Requests\Mcu\Package;

use App\Http\Requests\Mcu\Package\StoreMcuPackageRequest;
use App\Http\Requests\Mcu\Package\UpdateMcuPackageRequest;
use App\Models\McuPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PackageRequestsTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_rules(): void
    {
        $rules = (new StoreMcuPackageRequest())->rules();
        $this->assertArrayHasKey('code', $rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('base_price', $rules);
        $this->assertStringContainsString('unique:mcu_packages', $rules['code']);
    }

    public function test_update_rules_ignore_self(): void
    {
        $pkg = McuPackage::create(['code' => 'PKT-001', 'name' => 'Test', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $request = new UpdateMcuPackageRequest();
        $request->setRouteResolver(fn() => new class($pkg) {
            public function __construct(public $pkg) {}
            public function parameter($key) { return $this->pkg; }
        });
        $rules = $request->rules();
        $this->assertIsArray($rules['code']);
        $this->assertCount(4, $rules['code']); // required, string, max, Rule
    }

    public function test_validation_passes_valid(): void
    {
        $data = ['code' => 'PKT-VALID', 'name' => 'Valid', 'base_price' => 100000, 'display_order' => 1, 'status' => true];
        $validator = Validator::make($data, (new StoreMcuPackageRequest())->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_validation_fails_missing_required(): void
    {
        $validator = Validator::make([], (new StoreMcuPackageRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('code', $validator->errors()->toArray());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('base_price', $validator->errors()->toArray());
    }

    public function test_validation_fails_duplicate_code(): void
    {
        McuPackage::create(['code' => 'PKT-DUP', 'name' => 'Dup', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $validator = Validator::make(['code' => 'PKT-DUP', 'name' => 'Dup2', 'base_price' => 100000, 'display_order' => 1, 'status' => true], (new StoreMcuPackageRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('code', $validator->errors()->toArray());
    }

    public function test_validation_fails_negative_price(): void
    {
        $validator = Validator::make(['code' => 'PKT-NEG', 'name' => 'Neg', 'base_price' => -100, 'display_order' => 1, 'status' => true], (new StoreMcuPackageRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('base_price', $validator->errors()->toArray());
    }

    public function test_validation_items_nullable(): void
    {
        $data = ['code' => 'PKT-ITEMS', 'name' => 'With Items', 'base_price' => 100000, 'display_order' => 1, 'status' => true, 'items' => ['McuLab:1']];
        $validator = Validator::make($data, (new StoreMcuPackageRequest())->rules());
        $this->assertTrue($validator->passes());

        $data2 = ['code' => 'PKT-NOITEMS', 'name' => 'No Items', 'base_price' => 100000, 'display_order' => 1, 'status' => true];
        $validator2 = Validator::make($data2, (new StoreMcuPackageRequest())->rules());
        $this->assertTrue($validator2->passes());
    }
}
