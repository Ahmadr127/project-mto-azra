<?php

namespace Tests\Unit\Services\Mcu\Examination;

use App\Models\McuAnamnesis;
use App\Models\McuLab;
use App\Models\McuMedicalAction;
use App\Models\McuPackage;
use App\Models\McuPhysicalExam;
use App\Models\McuRadiology;
use App\Models\McuRegistration;
use App\Models\Patient;
use App\Models\User;
use App\Services\Mcu\Examination\McuExaminationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExaminationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected McuExaminationService $service;
    protected McuRegistration $registration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(McuExaminationService::class);

        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Test Pasien']);
        $lab = McuLab::create(['code' => 'LAB-001', 'name' => 'GDS', 'price' => 25000, 'display_order' => 1, 'status' => true, 'normal_value' => '<140']);
        $rad = McuRadiology::create(['code' => 'RAD-001', 'name' => 'Thorax', 'price' => 150000, 'display_order' => 1, 'status' => true]);
        $ana = McuAnamnesis::create(['code' => 'ANA-001', 'name' => 'Anamnesis', 'price' => 0, 'display_order' => 1, 'status' => true]);
        $fis = McuPhysicalExam::create(['code' => 'FIS-001', 'name' => 'Fisik', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        $act = McuMedicalAction::create(['code' => 'TND-001', 'name' => 'Konsultasi', 'price' => 50000, 'display_order' => 1, 'status' => true]);

        $package = McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $package->items()->create(['item_type' => McuLab::class, 'item_id' => $lab->id]);
        $package->items()->create(['item_type' => McuRadiology::class, 'item_id' => $rad->id]);
        $package->items()->create(['item_type' => McuAnamnesis::class, 'item_id' => $ana->id]);
        $package->items()->create(['item_type' => McuMedicalAction::class, 'item_id' => $act->id]);
        $package->items()->create(['item_type' => McuPhysicalExam::class, 'item_id' => $fis->id]);

        $this->registration = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'registered']);
        $this->registration->examLabs()->create(['mcu_lab_id' => $lab->id, 'normal_value' => $lab->normal_value, 'status' => 'pending']);
        $this->registration->examRadiologies()->create(['mcu_radiology_id' => $rad->id, 'status' => 'pending']);
        $this->registration->examAnamneses()->create(['mcu_anamnesis_id' => $ana->id, 'status' => 'pending']);
        $this->registration->examMedicalActions()->create(['mcu_medical_action_id' => $act->id, 'status' => 'pending']);
        $this->registration->physicalExamResult()->create(['status' => 'pending']);
    }

    public function test_get_registrations_returns_only_registered_and_in_progress(): void
    {
        $patient = Patient::first();
        $package = McuPackage::first();
        $completed = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'completed']);
        $cancelled = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'cancelled']);

        $result = $this->service->getRegistrations();
        $this->assertTrue($result->contains('id', $this->registration->id));
        $this->assertFalse($result->contains('id', $completed->id));
        $this->assertFalse($result->contains('id', $cancelled->id));
        $this->assertCount(1, $result);
    }

    public function test_store_lab_updates_results(): void
    {
        $exam = $this->registration->examLabs->first();
        $this->service->storeLab($this->registration, [$exam->id => '120 mg/dL']);
        $this->assertDatabaseHas('mcu_exam_labs', ['id' => $exam->id, 'result_value' => '120 mg/dL', 'status' => 'completed']);
        $this->assertEquals('in_progress', $this->registration->fresh()->status);
    }

    public function test_store_lab_ignores_invalid_id(): void
    {
        $this->service->storeLab($this->registration, [99999 => 'value']);
        $this->assertDatabaseHas('mcu_exam_labs', ['id' => $this->registration->examLabs->first()->id, 'status' => 'pending']);
    }

    public function test_store_lab_empty_results_still_updates_status(): void
    {
        $this->service->storeLab($this->registration, []);
        $this->assertEquals('in_progress', $this->registration->fresh()->status);
    }

    public function test_store_radiology_normal(): void
    {
        $exam = $this->registration->examRadiologies->first();
        $this->service->storeRadiology($this->registration, [$exam->id => 'Normal findings'], [$exam->id => 1]);
        $exam->refresh();
        $this->assertEquals('completed', $exam->status);
        // Use raw attributes because accessor decodes
        $raw = $exam->getAttributes()['result'];
        $result = json_decode($raw, true);
        $this->assertTrue($result['is_normal']);
        $this->assertEquals('Normal findings', $result['findings']);
        // Also test accessors
        $this->assertTrue($exam->is_normal);
        $this->assertEquals('Normal findings', $exam->findings);
    }

    public function test_store_radiology_abnormal(): void
    {
        $exam = $this->registration->examRadiologies->first();
        $this->service->storeRadiology($this->registration, [$exam->id => 'Abnormal'], [$exam->id => 0]);
        $exam->refresh();
        $raw = $exam->getAttributes()['result'];
        $result = json_decode($raw, true);
        $this->assertFalse($result['is_normal']);
        $this->assertFalse($exam->is_normal);
    }

    public function test_store_radiology_without_is_normal_defaults_false(): void
    {
        $exam = $this->registration->examRadiologies->first();
        $this->service->storeRadiology($this->registration, [$exam->id => 'Test'], []);
        $exam->refresh();
        $raw = $exam->getAttributes()['result'];
        $result = json_decode($raw, true);
        $this->assertFalse($result['is_normal']);
        $this->assertFalse($exam->is_normal);
    }

    public function test_store_anamnesis(): void
    {
        $exam = $this->registration->examAnamneses->first();
        $this->service->storeAnamnesis($this->registration, [$exam->id => 'Ya (Ayah)']);
        $this->assertDatabaseHas('mcu_exam_anamneses', ['id' => $exam->id, 'result' => 'Ya (Ayah)', 'status' => 'completed']);
    }

    public function test_store_anamnesis_empty(): void
    {
        $this->service->storeAnamnesis($this->registration, []);
        $this->assertEquals('in_progress', $this->registration->fresh()->status);
    }

    public function test_store_physical(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $data = [
            'vital_signs' => ['height' => '170', 'weight' => '65'],
            'head_and_neck' => ['head' => 'Normal'],
            'thorax' => ['lung_auscultation' => 'Normal'],
            'abdomen' => ['hepar' => 'Normal'],
            'urogenital' => ['haemorrhoid' => 'Tidak'],
            'extremities' => ['up_right_str' => '5'],
            'others' => ['skin' => 'Normal'],
        ];
        $this->service->storePhysical($this->registration, $data);
        $result = $this->registration->physicalExamResult()->first();
        $this->assertEquals('completed', $result->status);
        $this->assertEquals('170', $result->vital_signs['height']);
        $this->assertEquals($user->id, $result->doctor_id);
    }

    public function test_store_physical_handles_missing_result(): void
    {
        $this->registration->physicalExamResult()->delete();
        $this->service->storePhysical($this->registration, ['vital_signs' => ['height' => '170']]);
        // Should not crash, just update status
        $this->assertEquals('in_progress', $this->registration->fresh()->status);
        $this->assertNull($this->registration->physicalExamResult);
    }

    public function test_store_doctor(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $exam = $this->registration->examMedicalActions->first();
        $this->service->storeDoctor($this->registration, [$exam->id => 'Hasil konsultasi']);
        $this->assertDatabaseHas('mcu_exam_medical_actions', ['id' => $exam->id, 'result' => 'Hasil konsultasi', 'status' => 'completed', 'doctor_id' => $user->id]);
    }

    public function test_store_doctor_ignores_invalid_id(): void
    {
        $this->service->storeDoctor($this->registration, [99999 => 'value']);
        $this->assertDatabaseHas('mcu_exam_medical_actions', ['id' => $this->registration->examMedicalActions->first()->id, 'status' => 'pending']);
    }

    public function test_store_doctor_empty(): void
    {
        $this->service->storeDoctor($this->registration, []);
        $this->assertEquals('in_progress', $this->registration->fresh()->status);
    }
}
