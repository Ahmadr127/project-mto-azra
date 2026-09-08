<?php

namespace Tests\Feature\Controllers\Mcu\Registration;

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

class McuRegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected McuPackage $package;
    protected Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $role->permissions()->attach(Permission::firstOrCreate(['name' => 'view_dashboard'], ['display_name' => 'View']));
        $this->admin = User::factory()->create(['role_id' => $role->id]);

        // Create master items
        $lab = McuLab::create(['code' => 'LAB-001', 'name' => 'GDS', 'price' => 25000, 'display_order' => 1, 'status' => true]);
        $this->package = McuPackage::create(['code' => 'PKT-001', 'name' => 'Paket Test', 'base_price' => 100000, 'display_order' => 1, 'status' => true]);
        $this->package->items()->create(['item_type' => McuLab::class, 'item_id' => $lab->id]);

        $this->patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Test Pasien']);
    }

    public function test_index_requires_auth(): void
    {
        $this->get(route('mcu-registrations.index'))->assertRedirect(route('login'));
        $this->actingAs($this->admin)->get(route('mcu-registrations.index'))->assertStatus(200);
    }

    public function test_index_search_by_patient(): void
    {
        $p1 = Patient::create(['patient_code' => 'RM-002', 'name' => 'Andi']);
        $p2 = Patient::create(['patient_code' => 'RM-003', 'name' => 'Budi']);
        McuRegistration::create(['patient_id' => $p1->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now(), 'status' => 'registered']);
        McuRegistration::create(['patient_id' => $p2->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now(), 'status' => 'registered']);

        $response = $this->actingAs($this->admin)->get(route('mcu-registrations.index', ['search' => 'Andi']));
        $this->assertCount(1, $response->viewData('registrations'));
    }

    public function test_index_filter_status(): void
    {
        McuRegistration::create(['patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now(), 'status' => 'registered']);
        McuRegistration::create(['patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now(), 'status' => 'completed']);

        $response = $this->actingAs($this->admin)->get(route('mcu-registrations.index', ['status' => 'completed']));
        $this->assertCount(1, $response->viewData('registrations'));
        $this->assertEquals('completed', $response->viewData('registrations')->first()->status);
    }

    public function test_index_provides_patients_and_packages_for_modal(): void
    {
        $response = $this->actingAs($this->admin)->get(route('mcu-registrations.index'));
        $response->assertViewHas(['patients', 'packages']);
    }

    public function test_create_returns_view(): void
    {
        $this->actingAs($this->admin)->get(route('mcu-registrations.create'))->assertStatus(200);
        $this->app['auth']->logout();
        $this->get(route('mcu-registrations.create'))->assertRedirect(route('login'));
        $this->actingAs($this->admin);
    }

    public function test_create_with_patient_id_preselect(): void
    {
        $response = $this->actingAs($this->admin)->get(route('mcu-registrations.create', ['patient_id' => $this->patient->id]));
        $response->assertViewHas('selectedPatientId', $this->patient->id);
    }

    public function test_store_creates_registration_and_exam_items(): void
    {
        $response = $this->actingAs($this->admin)->post(route('mcu-registrations.store'), [
            'patient_id' => $this->patient->id,
            'mcu_package_id' => $this->package->id,
            'registration_date' => now()->format('Y-m-d'),
        ]);
        $response->assertRedirect(route('mcu-registrations.index'));
        $this->assertDatabaseHas('mcu_registrations', ['patient_id' => $this->patient->id, 'status' => 'registered']);
        $reg = McuRegistration::first();
        $this->assertCount(1, $reg->examLabs);
        $this->assertEquals('pending', $reg->examLabs->first()->status);
    }

    public function test_store_generates_all_item_types(): void
    {
        // Add all types to package
        $this->package->items()->delete();
        $lab = McuLab::first();
        $rad = McuRadiology::create(['code' => 'RAD-001', 'name' => 'Thorax', 'price' => 150000, 'display_order' => 1, 'status' => true]);
        $ana = McuAnamnesis::create(['code' => 'ANA-001', 'name' => 'Anamnesis', 'price' => 0, 'display_order' => 1, 'status' => true]);
        $fis = McuPhysicalExam::create(['code' => 'FIS-001', 'name' => 'Fisik', 'price' => 10000, 'display_order' => 1, 'status' => true]);
        $act = McuMedicalAction::create(['code' => 'TND-001', 'name' => 'Tindakan', 'price' => 50000, 'display_order' => 1, 'status' => true]);

        foreach ([$lab, $rad, $ana, $fis, $act] as $item) {
            $type = match (get_class($item)) {
                McuLab::class => McuLab::class,
                McuRadiology::class => McuRadiology::class,
                McuAnamnesis::class => McuAnamnesis::class,
                McuPhysicalExam::class => McuPhysicalExam::class,
                McuMedicalAction::class => McuMedicalAction::class,
            };
            $this->package->items()->create(['item_type' => $type, 'item_id' => $item->id]);
        }

        $this->actingAs($this->admin)->post(route('mcu-registrations.store'), [
            'patient_id' => $this->patient->id,
            'mcu_package_id' => $this->package->id,
            'registration_date' => now()->format('Y-m-d'),
        ]);

        $reg = McuRegistration::latest()->first();
        $this->assertCount(1, $reg->examLabs);
        $this->assertCount(1, $reg->examRadiologies);
        $this->assertCount(1, $reg->examAnamneses);
        $this->assertCount(1, $reg->examMedicalActions);
        $this->assertNotNull($reg->physicalExamResult);
    }

    public function test_store_fails_validation(): void
    {
        $response = $this->actingAs($this->admin)->post(route('mcu-registrations.store'), []);
        $response->assertSessionHasErrors(['patient_id', 'mcu_package_id', 'registration_date']);

        $response = $this->actingAs($this->admin)->post(route('mcu-registrations.store'), [
            'patient_id' => 99999, 'mcu_package_id' => $this->package->id, 'registration_date' => now()->format('Y-m-d'),
        ]);
        $response->assertSessionHasErrors('patient_id');

        $response = $this->actingAs($this->admin)->post(route('mcu-registrations.store'), [
            'patient_id' => $this->patient->id, 'mcu_package_id' => 99999, 'registration_date' => now()->format('Y-m-d'),
        ]);
        $response->assertSessionHasErrors('mcu_package_id');

        $response = $this->actingAs($this->admin)->post(route('mcu-registrations.store'), [
            'patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => 'invalid-date',
        ]);
        $response->assertSessionHasErrors('registration_date');
    }

    public function test_store_requires_auth(): void
    {
        $this->post(route('mcu-registrations.store'), [
            'patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now()->format('Y-m-d'),
        ])->assertRedirect(route('login'));
    }

    public function test_show_returns_view(): void
    {
        $reg = McuRegistration::create(['patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now(), 'status' => 'registered']);
        $this->actingAs($this->admin)->get(route('mcu-registrations.show', $reg))->assertStatus(200);
        $this->app['auth']->logout();
        $this->get(route('mcu-registrations.show', $reg))->assertRedirect(route('login'));
        $this->actingAs($this->admin);
    }

    public function test_show_404(): void
    {
        $this->actingAs($this->admin)->get(route('mcu-registrations.show', 99999))->assertStatus(404);
    }

    public function test_edit_returns_view(): void
    {
        $reg = McuRegistration::create(['patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now(), 'status' => 'registered']);
        $this->actingAs($this->admin)->get(route('mcu-registrations.edit', $reg))->assertStatus(200);
    }

    public function test_update_success(): void
    {
        $reg = McuRegistration::create(['patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now(), 'status' => 'registered']);
        $newPatient = Patient::create(['patient_code' => 'RM-999', 'name' => 'New Patient']);
        $response = $this->actingAs($this->admin)->put(route('mcu-registrations.update', $reg), [
            'patient_id' => $newPatient->id,
            'mcu_package_id' => $this->package->id,
            'registration_date' => now()->format('Y-m-d'),
            'status' => 'in_progress',
        ]);
        $response->assertRedirect(route('mcu-registrations.index'));
        $this->assertDatabaseHas('mcu_registrations', ['id' => $reg->id, 'status' => 'in_progress', 'patient_id' => $newPatient->id]);
    }

    public function test_update_fails_validation(): void
    {
        $reg = McuRegistration::create(['patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now(), 'status' => 'registered']);
        $response = $this->actingAs($this->admin)->put(route('mcu-registrations.update', $reg), [
            'patient_id' => $this->patient->id,
            'mcu_package_id' => $this->package->id,
            'registration_date' => now()->format('Y-m-d'),
            'status' => 'invalid_status',
        ]);
        $response->assertSessionHasErrors('status');
    }

    public function test_update_requires_auth(): void
    {
        $reg = McuRegistration::create(['patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now(), 'status' => 'registered']);
        $this->put(route('mcu-registrations.update', $reg), [
            'patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now()->format('Y-m-d'), 'status' => 'registered',
        ])->assertRedirect(route('login'));
    }

    public function test_destroy_success(): void
    {
        $reg = McuRegistration::create(['patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now(), 'status' => 'registered']);
        $id = $reg->id;
        $response = $this->actingAs($this->admin)->delete(route('mcu-registrations.destroy', $reg));
        $response->assertRedirect(route('mcu-registrations.index'));
        $this->assertDatabaseMissing('mcu_registrations', ['id' => $id]);
    }

    public function test_destroy_requires_auth(): void
    {
        $reg = McuRegistration::create(['patient_id' => $this->patient->id, 'mcu_package_id' => $this->package->id, 'registration_date' => now(), 'status' => 'registered']);
        $this->delete(route('mcu-registrations.destroy', $reg))->assertRedirect(route('login'));
    }

    public function test_destroy_404(): void
    {
        $this->actingAs($this->admin)->delete(route('mcu-registrations.destroy', 99999))->assertStatus(404);
    }
}
