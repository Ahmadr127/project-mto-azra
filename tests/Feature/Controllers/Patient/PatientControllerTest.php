<?php

namespace Tests\Feature\Controllers\Patient;

use App\Models\Patient;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $role = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $perm = Permission::firstOrCreate(['name' => 'manage_patients'], ['display_name' => 'Kelola Pasien']);
        $role->permissions()->attach($perm);
        $this->admin = User::factory()->create(['role_id' => $role->id]);
    }

    // INDEX
    public function test_index_requires_auth(): void
    {
        $response = $this->get(route('patients.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_returns_view_with_patients(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'Andi']);
        Patient::create(['patient_code' => 'RM-002', 'name' => 'Budi']);

        $response = $this->actingAs($this->admin)->get(route('patients.index'));
        $response->assertStatus(200);
        $response->assertViewIs('mcu.patients.index');
        $response->assertViewHas('patients');
    }

    public function test_index_search_filters_by_name(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'Andi Wijaya']);
        Patient::create(['patient_code' => 'RM-002', 'name' => 'Budi Santoso']);

        $response = $this->actingAs($this->admin)->get(route('patients.index', ['search' => 'Andi']));
        $response->assertStatus(200);
        // Should contain Andi, not Budi
        $patients = $response->viewData('patients');
        $this->assertCount(1, $patients);
        $this->assertEquals('Andi Wijaya', $patients->first()->name);
    }

    public function test_index_per_column_filter(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'Andi', 'phone' => '08123456789', 'gender' => 'L']);
        Patient::create(['patient_code' => 'RM-002', 'name' => 'Budi', 'phone' => '08987654321', 'gender' => 'P']);

        $response = $this->actingAs($this->admin)->get(route('patients.index', ['filter_gender' => 'L']));
        $this->assertEquals(1, $response->viewData('patients')->count());

        $response = $this->actingAs($this->admin)->get(route('patients.index', ['filter_phone' => '0812']));
        $this->assertEquals(1, $response->viewData('patients')->count());
    }

    public function test_index_pagination_per_page(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            Patient::create(['patient_code' => 'RM-'.str_pad($i, 3, '0', STR_PAD_LEFT), 'name' => "Pasien $i"]);
        }

        $response = $this->actingAs($this->admin)->get(route('patients.index', ['per_page' => 25]));
        $this->assertEquals(15, $response->viewData('patients')->count());

        $response = $this->actingAs($this->admin)->get(route('patients.index', ['per_page' => 999]));
        // Invalid per_page should default to 10
        $this->assertEquals(10, $response->viewData('patients')->perPage());
    }

    // CREATE
    public function test_create_returns_view(): void
    {
        $response = $this->actingAs($this->admin)->get(route('patients.create'));
        $response->assertStatus(200);
        $response->assertViewIs('mcu.patients.create');
    }

    public function test_create_requires_auth(): void
    {
        $response = $this->get(route('patients.create'));
        $response->assertRedirect(route('login'));
    }

    // STORE
    public function test_store_creates_patient_successfully(): void
    {
        $data = [
            'patient_code' => 'RM-100',
            'name' => 'Test Pasien',
            'nik' => '3271000000000001',
            'gender' => 'L',
            'birth_place' => 'Bogor',
            'birth_date' => '1990-05-15',
            'address' => 'Jl Test',
            'phone' => '08123456789',
        ];

        $response = $this->actingAs($this->admin)->post(route('patients.store'), $data);
        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseHas('patients', ['patient_code' => 'RM-100', 'name' => 'Test Pasien']);
    }

    public function test_store_creates_patient_with_photo(): void
    {
        $photo = UploadedFile::fake()->image('foto.jpg');

        $response = $this->actingAs($this->admin)->post(route('patients.store'), [
            'patient_code' => 'RM-101',
            'name' => 'Foto Pasien',
            'photo' => $photo,
        ]);

        $response->assertRedirect(route('patients.index'));
        $patient = Patient::where('patient_code', 'RM-101')->first();
        $this->assertNotNull($patient->photo);
        Storage::disk('public')->assertExists($patient->photo);
    }

    public function test_store_fails_validation_when_required_missing(): void
    {
        $response = $this->actingAs($this->admin)->post(route('patients.store'), []);
        $response->assertSessionHasErrors(['patient_code', 'name']);
    }

    public function test_store_fails_when_patient_code_not_unique(): void
    {
        Patient::create(['patient_code' => 'RM-001', 'name' => 'Existing']);
        $response = $this->actingAs($this->admin)->post(route('patients.store'), [
            'patient_code' => 'RM-001',
            'name' => 'New',
        ]);
        $response->assertSessionHasErrors('patient_code');
    }

    public function test_store_fails_when_birth_date_future(): void
    {
        $future = now()->addDay()->format('Y-m-d');
        $response = $this->actingAs($this->admin)->post(route('patients.store'), [
            'patient_code' => 'RM-102',
            'name' => 'Future',
            'birth_date' => $future,
        ]);
        $response->assertSessionHasErrors('birth_date');
    }

    public function test_store_fails_when_gender_invalid(): void
    {
        $response = $this->actingAs($this->admin)->post(route('patients.store'), [
            'patient_code' => 'RM-103',
            'name' => 'Test',
            'gender' => 'X',
        ]);
        $response->assertSessionHasErrors('gender');
    }

    public function test_store_fails_when_photo_not_image(): void
    {
        $file = UploadedFile::fake()->create('doc.pdf', 100);
        $response = $this->actingAs($this->admin)->post(route('patients.store'), [
            'patient_code' => 'RM-104',
            'name' => 'Test',
            'photo' => $file,
        ]);
        $response->assertSessionHasErrors('photo');
    }

    public function test_store_requires_auth(): void
    {
        $response = $this->post(route('patients.store'), ['patient_code' => 'RM-105', 'name' => 'Test']);
        $response->assertRedirect(route('login'));
    }

    // SHOW
    public function test_show_returns_view_with_patient(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Show Test']);
        $response = $this->actingAs($this->admin)->get(route('patients.show', $patient));
        $response->assertStatus(200);
        $response->assertViewIs('mcu.patients.show');
        $response->assertViewHas('patient');
        $response->assertSee('Show Test');
    }

    public function test_show_requires_auth(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Test']);
        $response = $this->get(route('patients.show', $patient));
        $response->assertRedirect(route('login'));
    }

    public function test_show_404_when_patient_not_found(): void
    {
        $response = $this->actingAs($this->admin)->get('/patients/9999');
        $response->assertStatus(404);
    }

    // SEARCH
    public function test_search_redirects_to_show_when_found_by_code(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-SEARCH-001', 'name' => 'Search Test']);
        $response = $this->actingAs($this->admin)->get(route('patients.search', ['q' => 'RM-SEARCH-001']));
        $response->assertRedirect(route('patients.show', $patient));
    }

    public function test_search_finds_by_partial_code_or_nik(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-XYZ-123', 'name' => 'Test', 'nik' => '3271000000000001']);
        $response = $this->actingAs($this->admin)->get(route('patients.search', ['q' => 'XYZ']));
        $response->assertRedirect(route('patients.show', $patient));

        $response = $this->actingAs($this->admin)->get(route('patients.search', ['q' => '3271000000000001']));
        $response->assertRedirect(route('patients.show', $patient));
    }

    public function test_search_redirects_back_with_error_when_not_found(): void
    {
        $response = $this->actingAs($this->admin)->get(route('patients.search', ['q' => 'NOTFOUND123']));
        $response->assertRedirect(route('patients.index'));
        $response->assertSessionHas('error');
    }

    public function test_search_requires_q(): void
    {
        $response = $this->actingAs($this->admin)->get(route('patients.search'));
        $response->assertSessionHasErrors('q');
    }

    public function test_search_requires_auth(): void
    {
        $response = $this->get(route('patients.search', ['q' => 'RM-001']));
        $response->assertRedirect(route('login'));
    }

    // EDIT
    public function test_edit_returns_view(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Edit Test']);
        $response = $this->actingAs($this->admin)->get(route('patients.edit', $patient));
        $response->assertStatus(200);
        $response->assertViewIs('mcu.patients.edit');
    }

    public function test_edit_requires_auth(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Test']);
        $response = $this->get(route('patients.edit', $patient));
        $response->assertRedirect(route('login'));
    }

    // UPDATE
    public function test_update_successfully(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-001', 'name' => 'Old Name']);
        $response = $this->actingAs($this->admin)->put(route('patients.update', $patient), [
            'patient_code' => 'RM-001',
            'name' => 'New Name',
            'phone' => '08123456789',
        ]);
        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseHas('patients', ['id' => $patient->id, 'name' => 'New Name']);
    }

    public function test_update_with_new_photo(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-002', 'name' => 'Photo Old']);
        Storage::disk('public')->put('patients/old.jpg', 'old');
        $patient->update(['photo' => 'patients/old.jpg']);

        $newPhoto = UploadedFile::fake()->image('new.jpg');
        $response = $this->actingAs($this->admin)->put(route('patients.update', $patient), [
            'patient_code' => 'RM-002',
            'name' => 'Photo Old',
            'photo' => $newPhoto,
        ]);

        $response->assertRedirect(route('patients.index'));
        $patient->refresh();
        $this->assertNotEquals('patients/old.jpg', $patient->photo);
        Storage::disk('public')->assertMissing('patients/old.jpg');
        Storage::disk('public')->assertExists($patient->photo);
    }

    public function test_update_remove_photo(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-003', 'name' => 'Remove Photo']);
        Storage::disk('public')->put('patients/to_remove.jpg', 'data');
        $patient->update(['photo' => 'patients/to_remove.jpg']);

        $response = $this->actingAs($this->admin)->put(route('patients.update', $patient), [
            'patient_code' => 'RM-003',
            'name' => 'Remove Photo',
            'remove_photo' => '1',
        ]);

        $response->assertRedirect(route('patients.index'));
        $patient->refresh();
        $this->assertNull($patient->photo);
        Storage::disk('public')->assertMissing('patients/to_remove.jpg');
    }

    public function test_update_fails_validation_when_code_taken_by_other(): void
    {
        $p1 = Patient::create(['patient_code' => 'RM-001', 'name' => 'A']);
        $p2 = Patient::create(['patient_code' => 'RM-002', 'name' => 'B']);

        $response = $this->actingAs($this->admin)->put(route('patients.update', $p1), [
            'patient_code' => 'RM-002',
            'name' => 'A Updated',
        ]);
        $response->assertSessionHasErrors('patient_code');
    }

    public function test_update_fails_when_required_missing(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-004', 'name' => 'Test']);
        $response = $this->actingAs($this->admin)->put(route('patients.update', $patient), [
            'patient_code' => '',
            'name' => '',
        ]);
        $response->assertSessionHasErrors(['patient_code', 'name']);
    }

    public function test_update_requires_auth(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-005', 'name' => 'Test']);
        $response = $this->put(route('patients.update', $patient), ['patient_code' => 'RM-005', 'name' => 'Test']);
        $response->assertRedirect(route('login'));
    }

    // DESTROY
    public function test_destroy_deletes_patient_and_photo(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-006', 'name' => 'Delete Me']);
        Storage::disk('public')->put('patients/del.jpg', 'data');
        $patient->update(['photo' => 'patients/del.jpg']);
        $id = $patient->id;

        $response = $this->actingAs($this->admin)->delete(route('patients.destroy', $patient));
        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseMissing('patients', ['id' => $id]);
        Storage::disk('public')->assertMissing('patients/del.jpg');
    }

    public function test_destroy_without_photo(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-007', 'name' => 'No Photo']);
        $id = $patient->id;

        $response = $this->actingAs($this->admin)->delete(route('patients.destroy', $patient));
        $response->assertRedirect(route('patients.index'));
        $this->assertDatabaseMissing('patients', ['id' => $id]);
    }

    public function test_destroy_requires_auth(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-008', 'name' => 'Test']);
        $response = $this->delete(route('patients.destroy', $patient));
        $response->assertRedirect(route('login'));
    }

    public function test_destroy_404_when_not_found(): void
    {
        $response = $this->actingAs($this->admin)->delete('/patients/9999');
        $response->assertStatus(404);
    }
}
