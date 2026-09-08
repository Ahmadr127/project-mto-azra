<?php

namespace Tests\Unit\Requests\Mcu\Resume;

use App\Http\Requests\Mcu\Resume\StoreResumeRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ResumeRequestsTest extends TestCase
{
    use RefreshDatabase;
    public function test_rules_are_nullable(): void
    {
        $rules = (new StoreResumeRequest())->rules();
        $this->assertArrayHasKey('conclusion', $rules);
        $this->assertArrayHasKey('recommendation', $rules);
        $this->assertEquals('nullable|string', $rules['conclusion']);
        $this->assertEquals('nullable|string', $rules['recommendation']);
    }

    public function test_validation_passes_with_empty(): void
    {
        $validator = Validator::make([], (new StoreResumeRequest())->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_validation_passes_with_valid_data(): void
    {
        $data = ['conclusion' => 'Sehat', 'recommendation' => 'Olahraga'];
        $validator = Validator::make($data, (new StoreResumeRequest())->rules());
        $this->assertTrue($validator->passes());
    }

    public function test_validation_fails_when_not_string(): void
    {
        $validator = Validator::make(['conclusion' => ['array']], (new StoreResumeRequest())->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('conclusion', $validator->errors()->toArray());
    }

    public function test_authorize(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $this->assertTrue((new StoreResumeRequest())->authorize());
    }
}
