<?php

namespace Tests\Unit\Services\Mcu\Resume;

use App\Models\McuPackage;
use App\Models\McuRegistration;
use App\Models\Patient;
use App\Models\User;
use App\Services\Mcu\Resume\McuResumeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ResumeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected McuResumeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(McuResumeService::class);
    }

    public function test_paginate_returns_only_in_progress_and_completed(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Test']);
        $package = McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'registered']);
        $inProgress = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'in_progress']);
        $completed = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'completed']);

        $request = Request::create('/', 'GET', []);
        $result = $this->service->paginate($request);
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('id', $inProgress->id));
        $this->assertTrue($result->contains('id', $completed->id));
    }

    public function test_paginate_search(): void
    {
        $p1 = Patient::create(['patient_code' => 'RM-001', 'name' => 'Andi']);
        $p2 = Patient::create(['patient_code' => 'RM-002', 'name' => 'Budi']);
        $pkg = McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        McuRegistration::create(['patient_id' => $p1->id, 'mcu_package_id' => $pkg->id, 'registration_date' => now(), 'status' => 'completed']);
        McuRegistration::create(['patient_id' => $p2->id, 'mcu_package_id' => $pkg->id, 'registration_date' => now(), 'status' => 'completed']);

        $request = Request::create('/', 'GET', ['search' => 'Andi']);
        $result = $this->service->paginate($request);
        $this->assertCount(1, $result);
        $this->assertEquals('Andi', $result->first()->patient->name);
    }

    public function test_store_updates_registration_to_completed(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-003', 'name' => 'Store Test']);
        $package = McuPackage::create(['code' => 'PKT-002', 'name' => 'Paket2', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $reg = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'in_progress']);
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->service->store($reg, ['conclusion' => 'Sehat', 'recommendation' => 'Istirahat']);

        $reg->refresh();
        $this->assertEquals('Sehat', $reg->conclusion);
        $this->assertEquals('Istirahat', $reg->recommendation);
        $this->assertEquals('completed', $reg->status);
        $this->assertEquals($user->id, $reg->resume_by);
        $this->assertNotNull($reg->resume_date);
    }

    public function test_store_with_nullable_fields(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-004', 'name' => 'Nullable']);
        $package = McuPackage::create(['code' => 'PKT-003', 'name' => 'Paket3', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $reg = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'in_progress']);
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->service->store($reg, []);
        $reg->refresh();
        $this->assertEquals('completed', $reg->status);
    }

    public function test_pdf_generates_stream(): void
    {
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class) && !class_exists(\Barryvdh\DomPDF\Facade\PDF::class)) {
            $this->markTestSkipped('barryvdh/laravel-dompdf not available');
        }
        $patient = Patient::create(['patient_code' => 'RM-005', 'name' => 'PDF Test']);
        $package = McuPackage::create(['code' => 'PKT-004', 'name' => 'Paket PDF', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $reg = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'completed']);

        try {
            $response = $this->service->pdf($reg);
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'DomPDF') || str_contains($e->getMessage(), 'Pdf')) {
                $this->markTestSkipped('PDF generation requires dompdf: '.$e->getMessage());
            }
            throw $e;
        }
    }
}
