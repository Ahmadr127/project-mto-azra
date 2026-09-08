<?php

namespace Tests\Unit\Services\Patient;

use App\Models\Patient;
use App\Services\Patient\PatientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PatientServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PatientService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PatientService::class);
        Storage::fake('public');
    }

    public function test_create_patient_without_photo(): void
    {
        $data = [
            'patient_code' => 'RM-001',
            'name' => 'Test Pasien',
            'nik' => '3271000000000001',
            'gender' => 'L',
            'birth_place' => 'Bogor',
            'birth_date' => '1990-05-15',
            'address' => 'Jl Test',
            'kelurahan' => 'Kel A',
            'kecamatan' => 'Kec B',
            'kabupaten_kota' => 'Kota Bogor',
            'provinsi' => 'Jawa Barat',
            'kode_pos' => '16111',
            'phone' => '08123456789',
            'emergency_phone' => '081987654321',
            'department' => 'IT',
            'employee_status' => 'Tetap',
            'bpjs' => '1234567890',
        ];

        $patient = $this->service->create($data, null);

        $this->assertDatabaseHas('patients', ['patient_code' => 'RM-001', 'name' => 'Test Pasien']);
        $this->assertNull($patient->photo);
        $this->assertEquals('RM-001', $patient->patient_code);
    }

    public function test_create_patient_with_photo(): void
    {
        $data = ['patient_code' => 'RM-002', 'name' => 'Foto Pasien'];
        $photo = UploadedFile::fake()->image('foto.jpg');

        $patient = $this->service->create($data, $photo);

        $this->assertNotNull($patient->photo);
        Storage::disk('public')->assertExists($patient->photo);
        $this->assertStringStartsWith('patients/', $patient->photo);
    }

    public function test_update_patient_without_photo_keeps_existing(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-003', 'name' => 'Awal', 'phone' => '0811']);
        $patient->update(['photo' => 'patients/old.jpg']);
        Storage::disk('public')->put('patients/old.jpg', 'dummy');

        $updated = $this->service->update($patient, ['name' => 'Updated', 'phone' => '0822'], null, false);

        $this->assertEquals('Updated', $updated->name);
        $this->assertEquals('patients/old.jpg', $updated->photo);
        Storage::disk('public')->assertExists('patients/old.jpg');
    }

    public function test_update_patient_with_new_photo_replaces_old(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-004', 'name' => 'Foto Lama']);
        Storage::disk('public')->put('patients/old.jpg', 'old');
        $patient->update(['photo' => 'patients/old.jpg']);

        $newPhoto = UploadedFile::fake()->image('new.jpg');
        $updated = $this->service->update($patient, ['name' => 'Foto Baru'], $newPhoto, false);

        $this->assertNotEquals('patients/old.jpg', $updated->photo);
        Storage::disk('public')->assertMissing('patients/old.jpg');
        Storage::disk('public')->assertExists($updated->photo);
    }

    public function test_update_patient_remove_photo_flag(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-005', 'name' => 'Hapus Foto']);
        Storage::disk('public')->put('patients/to_delete.jpg', 'data');
        $patient->update(['photo' => 'patients/to_delete.jpg']);

        $updated = $this->service->update($patient, ['name' => 'Hapus Foto'], null, true);

        $this->assertNull($updated->photo);
        Storage::disk('public')->assertMissing('patients/to_delete.jpg');
        $this->assertDatabaseHas('patients', ['id' => $patient->id, 'photo' => null]);
    }

    public function test_delete_patient_removes_photo_and_record(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-006', 'name' => 'Delete Me']);
        Storage::disk('public')->put('patients/del.jpg', 'data');
        $patient->update(['photo' => 'patients/del.jpg']);

        $this->service->delete($patient);

        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
        Storage::disk('public')->assertMissing('patients/del.jpg');
    }

    public function test_delete_patient_without_photo(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-007', 'name' => 'No Photo']);
        $id = $patient->id;

        $this->service->delete($patient);

        $this->assertDatabaseMissing('patients', ['id' => $id]);
    }

    public function test_get_age_detail_returns_correct_array(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-008', 'name' => 'Age Test', 'birth_date' => '1990-01-15']);
        $detail = $this->service->getAgeDetail($patient);

        $this->assertIsArray($detail);
        $this->assertArrayHasKey('years', $detail);
        $this->assertArrayHasKey('months', $detail);
        $this->assertArrayHasKey('days', $detail);
        $this->assertGreaterThan(30, $detail['years']);
    }

    public function test_get_age_detail_returns_null_when_no_birth_date(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-009', 'name' => 'No Birth']);
        $detail = $this->service->getAgeDetail($patient);
        $this->assertNull($detail);
    }

    public function test_delete_photo_handles_missing_file_gracefully(): void
    {
        $patient = Patient::create(['patient_code' => 'RM-010', 'name' => 'Missing File', 'photo' => 'patients/not_exist.jpg']);
        // Should not throw
        $this->service->deletePhoto($patient);
        $this->assertTrue(true);
    }
}
