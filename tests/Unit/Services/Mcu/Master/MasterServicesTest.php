<?php

namespace Tests\Unit\Services\Mcu\Master;

use App\Models\McuAnamnesis;
use App\Models\McuLab;
use App\Models\McuMedicalAction;
use App\Models\McuPhysicalExam;
use App\Models\McuRadiology;
use App\Services\Mcu\Master\McuAnamnesisService;
use App\Services\Mcu\Master\McuLabService;
use App\Services\Mcu\Master\McuMedicalActionService;
use App\Services\Mcu\Master\McuPhysicalExamService;
use App\Services\Mcu\Master\McuRadiologyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class MasterServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_mcu_lab_service_paginate_with_search(): void
    {
        McuLab::create(['code' => 'LAB-001', 'name' => 'GDS', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        McuLab::create(['code' => 'LAB-002', 'name' => 'Cholesterol', 'price' => 20000, 'display_order' => 2, 'status' => true]);

        $service = app(McuLabService::class);
        $request = Request::create('/', 'GET', ['search' => 'GDS']);
        $result = $service->paginate($request);

        $this->assertCount(1, $result);
        $this->assertEquals('GDS', $result->first()->name);

        $request = Request::create('/', 'GET', ['search' => 'LAB-002']);
        $result = $service->paginate($request);
        $this->assertCount(1, $result);
        $this->assertEquals('LAB-002', $result->first()->code);
    }

    public function test_mcu_lab_service_paginate_without_search_returns_all(): void
    {
        McuLab::create(['code' => 'LAB-001', 'name' => 'A', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        McuLab::create(['code' => 'LAB-002', 'name' => 'B', 'price' => 10000, 'display_order' => 2, 'status' => true]);
        $service = app(McuLabService::class);
        $result = $service->paginate(Request::create('/', 'GET', []));
        $this->assertCount(2, $result);
    }

    public function test_mcu_lab_service_create_update_delete(): void
    {
        $service = app(McuLabService::class);
        $lab = $service->create(['code' => 'LAB-NEW', 'name' => 'New Lab', 'price' => 15000, 'display_order' => 1, 'status' => true]);
        $this->assertDatabaseHas('mcu_labs', ['code' => 'LAB-NEW']);
        $this->assertEquals('New Lab', $lab->name);

        $service->update($lab, ['code' => 'LAB-NEW-UPD', 'name' => 'Updated', 'price' => 16000, 'display_order' => 2, 'status' => false]);
        $this->assertDatabaseHas('mcu_labs', ['code' => 'LAB-NEW-UPD', 'name' => 'Updated']);
        $this->assertEquals(false, $lab->fresh()->status);

        $id = $lab->id;
        $service->delete($lab);
        $this->assertDatabaseMissing('mcu_labs', ['id' => $id]);
    }

    public function test_mcu_radiology_service_crud(): void
    {
        $service = app(McuRadiologyService::class);
        $rad = $service->create(['code' => 'RAD-001', 'name' => 'Thorax', 'price' => 150000, 'display_order' => 1, 'status' => true]);
        $this->assertDatabaseHas('mcu_radiologies', ['code' => 'RAD-001']);
        $service->update($rad, ['code' => 'RAD-001-UPD', 'name' => 'Thorax Updated', 'price' => 160000, 'display_order' => 2, 'status' => true]);
        $this->assertDatabaseHas('mcu_radiologies', ['code' => 'RAD-001-UPD']);
        $service->delete($rad);
        $this->assertDatabaseMissing('mcu_radiologies', ['code' => 'RAD-001-UPD']);
    }

    public function test_mcu_anamnesis_service_crud(): void
    {
        $service = app(McuAnamnesisService::class);
        $ana = $service->create(['code' => 'ANA-001', 'name' => 'Anamnesis', 'price' => 0, 'display_order' => 1, 'status' => true]);
        $this->assertDatabaseHas('mcu_anamneses', ['code' => 'ANA-001']);
        $service->update($ana, ['code' => 'ANA-001-UPD', 'name' => 'Updated', 'price' => 0, 'display_order' => 1, 'status' => true]);
        $this->assertDatabaseHas('mcu_anamneses', ['code' => 'ANA-001-UPD']);
        $service->delete($ana);
        $this->assertDatabaseMissing('mcu_anamneses', ['code' => 'ANA-001-UPD']);
    }

    public function test_mcu_physical_exam_service_crud(): void
    {
        $service = app(McuPhysicalExamService::class);
        $fis = $service->create(['code' => 'FIS-001', 'name' => 'Fisik', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        $this->assertDatabaseHas('mcu_physical_exams', ['code' => 'FIS-001']);
        $service->update($fis, ['code' => 'FIS-001-UPD', 'name' => 'Fisik Updated', 'price' => 11000, 'display_order' => 1, 'status' => true]);
        $this->assertDatabaseHas('mcu_physical_exams', ['code' => 'FIS-001-UPD']);
        $service->delete($fis);
        $this->assertDatabaseMissing('mcu_physical_exams', ['code' => 'FIS-001-UPD']);
    }

    public function test_mcu_medical_action_service_crud(): void
    {
        $service = app(McuMedicalActionService::class);
        $act = $service->create(['code' => 'TND-001', 'name' => 'Tindakan', 'price' => 50000, 'display_order' => 1, 'status' => true]);
        $this->assertDatabaseHas('mcu_medical_actions', ['code' => 'TND-001']);
        $service->update($act, ['code' => 'TND-001-UPD', 'name' => 'Updated', 'price' => 55000, 'display_order' => 1, 'status' => true]);
        $this->assertDatabaseHas('mcu_medical_actions', ['code' => 'TND-001-UPD']);
        $service->delete($act);
        $this->assertDatabaseMissing('mcu_medical_actions', ['code' => 'TND-001-UPD']);
    }

    public function test_paginate_returns_paginator_and_preserves_query_string(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            McuLab::create(['code' => 'LAB-'.$i, 'name' => "Lab $i", 'price' => 10000, 'display_order' => $i, 'status' => true]);
        }
        $service = app(McuLabService::class);
        $request = Request::create('/', 'GET', ['search' => 'Lab']);
        $result = $service->paginate($request);
        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(15, $result->total());
    }
}
