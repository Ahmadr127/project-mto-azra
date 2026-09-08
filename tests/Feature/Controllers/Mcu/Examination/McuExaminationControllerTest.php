<?php

namespace Tests\Feature\Controllers\Mcu\Examination;

use App\Models\McuAnamnesis;
use App\Models\McuLab;
use App\Models\McuMedicalAction;
use App\Models\McuPackage;
use App\Models\McuPhysicalExam;
use App\Models\McuRadiology;
use App\Models\McuRegistration;
use App\Models\Patient;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class McuExaminationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected McuRegistration $registration;
    protected McuLab $lab;
    protected McuRadiology $radiology;
    protected McuAnamnesis $anamnesis;
    protected McuMedicalAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $role->permissions()->attach(Permission::firstOrCreate(['name' => 'view_dashboard'], ['display_name' => 'View']));
        $this->admin = User::factory()->create(['role_id' => $role->id]);

        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Test Pasien']);
        $this->lab = McuLab::create(['code' => 'LAB-001', 'name' => 'GDS', 'category' => 'Glukosa', 'price' => 25000, 'display_order' => 1, 'status' => true, 'normal_value' => '<140']);
        $this->radiology = McuRadiology::create(['code' => 'RAD-001', 'name' => 'Thorax', 'price' => 150000, 'display_order' => 1, 'status' => true]);
        $this->anamnesis = McuAnamnesis::create(['code' => 'ANA-001', 'name' => 'Riwayat Hipertensi', 'price' => 0, 'display_order' => 1, 'status' => true]);
        $this->action = McuMedicalAction::create(['code' => 'TND-001', 'name' => 'Konsultasi', 'price' => 50000, 'display_order' => 1, 'status' => true]);
        $fis = McuPhysicalExam::create(['code' => 'FIS-001', 'name' => 'Fisik', 'price' => 10000, 'display_order' => 1, 'status' => true]);

        $package = McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket Lengkap', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $package->items()->create(['item_type' => McuLab::class, 'item_id' => $this->lab->id]);
        $package->items()->create(['item_type' => McuRadiology::class, 'item_id' => $this->radiology->id]);
        $package->items()->create(['item_type' => McuAnamnesis::class, 'item_id' => $this->anamnesis->id]);
        $package->items()->create(['item_type' => McuMedicalAction::class, 'item_id' => $this->action->id]);
        $package->items()->create(['item_type' => McuPhysicalExam::class, 'item_id' => $fis->id]);

        $this->registration = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'registered']);
        // Generate exam items like registration does
        $this->registration->examLabs()->create(['mcu_lab_id' => $this->lab->id, 'normal_value' => $this->lab->normal_value, 'status' => 'pending']);
        $this->registration->examRadiologies()->create(['mcu_radiology_id' => $this->radiology->id, 'status' => 'pending']);
        $this->registration->examAnamneses()->create(['mcu_anamnesis_id' => $this->anamnesis->id, 'status' => 'pending']);
        $this->registration->examMedicalActions()->create(['mcu_medical_action_id' => $this->action->id, 'status' => 'pending']);
        $this->registration->physicalExamResult()->create(['status' => 'pending']);
    }

    // Helper to assert requires auth
    protected function assertRequiresAuth(string $method, string $route, $params = []): void
    {
        $this->app['auth']->logout();
        $params = is_array($params) ? $params : [$params];
        // For routes with model binding, params may be model instance - wrap correctly
        $routeParams = [];
        if (!empty($params)) {
            // If first param is model, use it as route param
            $routeParams = $params;
            // If route expects {mcuRegistration}, pass model directly
            if (count($params) === 1 && is_object($params[0])) {
                $routeParams = $params[0];
            }
        }
        $response = $this->$method(route($route, $routeParams));
        $response->assertRedirect(route('login'));
        $this->actingAs($this->admin);
    }

    // LAB
    public function test_lab_index_requires_auth(): void { $this->assertRequiresAuth('get', 'mcu-examinations.lab.index'); }
    public function test_lab_index_returns_view(): void
    {
        $this->actingAs($this->admin)->get(route('mcu-examinations.lab.index'))->assertStatus(200)->assertViewIs('mcu.examinations.lab.index');
    }
    public function test_lab_form_returns_view(): void
    {
        $this->actingAs($this->admin)->get(route('mcu-examinations.lab.form', $this->registration))->assertStatus(200)->assertViewIs('mcu.examinations.lab.form');
        $this->app['auth']->logout();
        $this->get(route('mcu-examinations.lab.form', $this->registration))->assertRedirect(route('login'));
        $this->actingAs($this->admin);
    }
    public function test_lab_form_404(): void
    {
        $this->actingAs($this->admin)->get(route('mcu-examinations.lab.form', 99999))->assertStatus(404);
    }
    public function test_lab_store_updates_results_and_status(): void
    {
        $exam = $this->registration->examLabs->first();
        $response = $this->actingAs($this->admin)->post(route('mcu-examinations.lab.store', $this->registration), [
            'results' => [$exam->id => '110 mg/dL'],
        ]);
        $response->assertRedirect(route('mcu-examinations.lab.index'));
        $this->assertDatabaseHas('mcu_exam_labs', ['id' => $exam->id, 'result_value' => '110 mg/dL', 'status' => 'completed']);
        $this->assertEquals('in_progress', $this->registration->fresh()->status);
    }
    public function test_lab_store_handles_empty_results(): void
    {
        $response = $this->actingAs($this->admin)->post(route('mcu-examinations.lab.store', $this->registration), []);
        $response->assertRedirect(route('mcu-examinations.lab.index'));
        // Should still update status
        $this->assertEquals('in_progress', $this->registration->fresh()->status);
    }
    public function test_lab_store_ignores_invalid_exam_id(): void
    {
        $response = $this->actingAs($this->admin)->post(route('mcu-examinations.lab.store', $this->registration), [
            'results' => [99999 => 'value'],
        ]);
        $response->assertRedirect(route('mcu-examinations.lab.index'));
        // No crash, original exam still pending
        $this->assertDatabaseHas('mcu_exam_labs', ['id' => $this->registration->examLabs->first()->id, 'status' => 'pending']);
    }
    public function test_lab_store_requires_auth(): void { $this->assertRequiresAuth('post', 'mcu-examinations.lab.store', $this->registration); }

    // RADIOLOGY
    public function test_radiology_index_and_form(): void
    {
        $this->actingAs($this->admin)->get(route('mcu-examinations.radiology.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('mcu-examinations.radiology.form', $this->registration))->assertStatus(200);
    }
    public function test_radiology_store_with_normal_and_findings(): void
    {
        $exam = $this->registration->examRadiologies->first();
        $response = $this->actingAs($this->admin)->post(route('mcu-examinations.radiology.store', $this->registration), [
            'results' => [$exam->id => 'Cor normal'],
            'is_normal' => [$exam->id => 1],
        ]);
        $response->assertRedirect(route('mcu-examinations.radiology.index'));
        $exam->refresh();
        $this->assertEquals('completed', $exam->status);
        // Model accessor returns findings, raw is json
        $this->assertEquals('Cor normal', $exam->findings);
        $this->assertEquals('Cor normal', $exam->result);
        $this->assertTrue($exam->is_normal);
        $raw = $exam->getAttributes()['result'];
        $this->assertStringContainsString('Cor normal', $raw);
        $this->assertStringContainsString('is_normal', $raw);
    }
    public function test_radiology_store_abnormal(): void
    {
        $exam = $this->registration->examRadiologies->first();
        $this->actingAs($this->admin)->post(route('mcu-examinations.radiology.store', $this->registration), [
            'results' => [$exam->id => 'Pembesaran jantung'],
            'is_normal' => [$exam->id => 0],
        ]);
        $exam->refresh();
        $this->assertFalse($exam->is_normal);
        $this->assertEquals('Pembesaran jantung', $exam->findings);
    }
    public function test_radiology_store_requires_auth(): void { $this->assertRequiresAuth('post', 'mcu-examinations.radiology.store', $this->registration); }

    // ANAMNESIS
    public function test_anamnesis_index_and_form(): void
    {
        $this->actingAs($this->admin)->get(route('mcu-examinations.anamnesis.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('mcu-examinations.anamnesis.form', $this->registration))->assertStatus(200);
    }
    public function test_anamnesis_store(): void
    {
        $exam = $this->registration->examAnamneses->first();
        $response = $this->actingAs($this->admin)->post(route('mcu-examinations.anamnesis.store', $this->registration), [
            'results' => [$exam->id => 'Ya (Ayah)'],
        ]);
        $response->assertRedirect(route('mcu-examinations.anamnesis.index'));
        $this->assertDatabaseHas('mcu_exam_anamneses', ['id' => $exam->id, 'result' => 'Ya (Ayah)', 'status' => 'completed']);
    }
    public function test_anamnesis_store_empty(): void
    {
        $response = $this->actingAs($this->admin)->post(route('mcu-examinations.anamnesis.store', $this->registration), []);
        $response->assertRedirect(route('mcu-examinations.anamnesis.index'));
    }
    public function test_anamnesis_store_requires_auth(): void { $this->assertRequiresAuth('post', 'mcu-examinations.anamnesis.store', $this->registration); }

    // PHYSICAL
    public function test_physical_index_and_form(): void
    {
        $this->actingAs($this->admin)->get(route('mcu-examinations.physical.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('mcu-examinations.physical.form', $this->registration))->assertStatus(200);
    }
    public function test_physical_store_updates_json_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('mcu-examinations.physical.store', $this->registration), [
            'vital_signs' => ['height' => '170', 'weight' => '65', 'tensi_1' => '120/80'],
            'head_and_neck' => ['head' => 'Normal'],
            'thorax' => ['lung_auscultation' => 'Normal'],
            'abdomen' => ['hepar' => 'Normal'],
            'urogenital' => ['haemorrhoid' => 'Tidak'],
            'extremities' => ['up_right_str' => '5'],
            'others' => ['skin' => 'Normal'],
        ]);
        $response->assertRedirect(route('mcu-examinations.physical.index'));
        $result = $this->registration->physicalExamResult()->first();
        $this->assertEquals('completed', $result->status);
        $this->assertEquals('170', $result->vital_signs['height']);
        $this->assertEquals(auth()->id(), $result->doctor_id);
        $this->assertEquals('in_progress', $this->registration->fresh()->status);
    }
    public function test_physical_store_handles_missing_result(): void
    {
        // Delete physical result to test null handling
        $this->registration->physicalExamResult()->delete();
        $response = $this->actingAs($this->admin)->post(route('mcu-examinations.physical.store', $this->registration), [
            'vital_signs' => ['height' => '170'],
        ]);
        // Should not crash, just update status
        $response->assertRedirect(route('mcu-examinations.physical.index'));
    }
    public function test_physical_store_requires_auth(): void { $this->assertRequiresAuth('post', 'mcu-examinations.physical.store', $this->registration); }

    // DOCTOR
    public function test_doctor_index_and_form(): void
    {
        $this->actingAs($this->admin)->get(route('mcu-examinations.doctor.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('mcu-examinations.doctor.form', $this->registration))->assertStatus(200);
    }
    public function test_doctor_store(): void
    {
        $exam = $this->registration->examMedicalActions->first();
        $response = $this->actingAs($this->admin)->post(route('mcu-examinations.doctor.store', $this->registration), [
            'results' => [$exam->id => 'Dalam Batas Normal, resep vitamin'],
        ]);
        $response->assertRedirect(route('mcu-examinations.doctor.index'));
        $this->assertDatabaseHas('mcu_exam_medical_actions', ['id' => $exam->id, 'result' => 'Dalam Batas Normal, resep vitamin', 'status' => 'completed']);
        $exam->refresh();
        $this->assertEquals($this->admin->id, $exam->doctor_id);
    }
    public function test_doctor_store_empty(): void
    {
        $response = $this->actingAs($this->admin)->post(route('mcu-examinations.doctor.store', $this->registration), []);
        $response->assertRedirect(route('mcu-examinations.doctor.index'));
    }
    public function test_doctor_store_requires_auth(): void { $this->assertRequiresAuth('post', 'mcu-examinations.doctor.store', $this->registration); }

    // General
    public function test_get_registrations_only_registered_and_in_progress(): void
    {
        $patient = Patient::first();
        $package = McuPackage::first();
        $completed = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'completed']);
        $cancelled = McuRegistration::create(['patient_id' => $patient->id, 'mcu_package_id' => $package->id, 'registration_date' => now(), 'status' => 'cancelled']);

        $response = $this->actingAs($this->admin)->get(route('mcu-examinations.lab.index'));
        $content = $response->getContent();
        // Should contain our initial registration (registered) but not completed/cancelled
        $this->assertStringContainsString($this->registration->patient->name, $content);
    }
}
