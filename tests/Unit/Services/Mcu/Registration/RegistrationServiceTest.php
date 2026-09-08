<?php

namespace Tests\Unit\Services\Mcu\Registration;

use App\Models\McuAnamnesis;
use App\Models\McuLab;
use App\Models\McuMedicalAction;
use App\Models\McuPackage;
use App\Models\McuPhysicalExam;
use App\Models\McuRadiology;
use App\Models\McuRegistration;
use App\Models\Patient;
use App\Services\Mcu\Registration\McuRegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class RegistrationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected McuRegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(McuRegistrationService::class);
    }

    public function test_paginate_with_search_and_status(): void
    {
        $p1 = Patient::create(['patient_code' => 'RM-001', 'name' => 'Andi']);
        $p2 = Patient::create(['patient_code' => 'RM-002', 'name' => 'Budi']);
        $pkg = McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        McuRegistration::create(['patient_id' => $p1->id, 'mcu_package_id' => $pkg->id, 'registration_date' => now(), 'status' => 'registered']);
        McuRegistration::create(['patient_id' => $p2->id, 'mcu_package_id' => $pkg->id, 'registration_date' => now(), 'status' => 'completed']);

        $request = Request::create('/', 'GET', ['search' => 'Andi']);
        $result = $this->service->paginate($request);
        $this->assertCount(1, $result);
        $this->assertEquals('Andi', $result->first()->patient->name);

        $request = Request::create('/', 'GET', ['status' => 'completed']);
        $result = $this->service->paginate($request);
        $this->assertCount(1, $result);
        $this->assertEquals('completed', $result->first()->status);
    }

    public function test_create_generates_exam_items(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Test']);
        $lab = McuLab::create(['code' => 'LAB-001', 'name' => 'GDS', 'price' => 25000, 'display_order' => 1, 'status' => true, 'normal_value' => '<140']);
        $package = McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $package->items()->create(['item_type' => McuLab::class, 'item_id' => $lab->id]);

        $reg = $this->service->create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now()->format('Y-m-d')]);

        $this->assertDatabaseHas('mcu_registrations', ['id' => $reg->id, 'status' => 'registered']);
        $this->assertCount(1, $reg->examLabs);
        $this->assertEquals($lab->id, $reg->examLabs->first()->mcu_lab_id);
        $this->assertEquals('<140', $reg->examLabs->first()->normal_value);
        $this->assertEquals('pending', $reg->examLabs->first()->status);
    }

    public function test_create_generates_all_types(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-002', 'name' => 'All Types']);
        $lab = McuLab::create(['code' => 'LAB-001', 'name' => 'GDS', 'price' => 25000, 'display_order' => 1, 'status' => true]);
        $rad = McuRadiology::create(['code' => 'RAD-001', 'name' => 'Thorax', 'price' => 150000, 'display_order' => 1, 'status' => true]);
        $ana = McuAnamnesis::create(['code' => 'ANA-001', 'name' => 'Anamnesis', 'price' => 0, 'display_order' => 1, 'status' => true]);
        $fis = McuPhysicalExam::create(['code' => 'FIS-001', 'name' => 'Fisik', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        $act = McuMedicalAction::create(['code' => 'TND-001', 'name' => 'Tindakan', 'price' => 50000, 'display_order' => 1, 'status' => true]);

        $package = McuPackage::create(['code' => 'PKT-ALL', 'name' => 'Paket All', 'base_price' => 200000, 'display_order' => 1, 'status' => true]);
        foreach ([$lab, $rad, $ana, $fis, $act] as $item) {
            $type = match (get_class($item)) {
                McuLab::class => McuLab::class,
                McuRadiology::class => McuRadiology::class,
                McuAnamnesis::class => McuAnamnesis::class,
                McuPhysicalExam::class => McuPhysicalExam::class,
                McuMedicalAction::class => McuMedicalAction::class,
            };
            $package->items()->create(['item_type' => $type, 'item_id' => $item->id]);
        }

        $reg = $this->service->create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now()->format('Y-m-d')]);

        $this->assertCount(1, $reg->examLabs);
        $this->assertCount(1, $reg->examRadiologies);
        $this->assertCount(1, $reg->examAnamneses);
        $this->assertCount(1, $reg->examMedicalActions);
        $this->assertNotNull($reg->physicalExamResult);
    }

    public function test_update(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-003', 'name' => 'Update Test']);
        $package = McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $reg = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'registered']);
        $newPatient = Patient::create(['patient_code' => 'RM-004', 'name' => 'New Patient']);

        $this->service->update($reg, ['patient_id' => $newPatient->id, 'mcu_package_id' => $package->id, 'registration_date' => now()->format('Y-m-d'), 'status' => 'in_progress']);

        $this->assertDatabaseHas('mcu_registrations', ['id' => $reg->id, 'patient_id' => $newPatient->id, 'status' => 'in_progress']);
    }

    public function test_delete(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-005', 'name' => 'Delete']);
        $package = McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $reg = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'registered']);
        $id = $reg->id;
        $this->service->delete($reg);
        $this->assertDatabaseMissing('mcu_registrations', ['id' => $id]);
    }
}
