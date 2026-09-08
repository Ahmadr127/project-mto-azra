<?php

namespace App\Services\Patient;

use App\Models\Patient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PatientService
{
    /**
     * Buat pasien baru + handle foto.
     */
    public function create(array $data, ?UploadedFile $photo = null): Patient
    {
        if ($photo) {
            $data['photo'] = $photo->store('patients', 'public');
        }

        return Patient::create($data);
    }

    /**
     * Update pasien + handle foto (upload baru / hapus).
     */
    public function update(Patient $patient, array $data, ?UploadedFile $photo = null, bool $removePhoto = false): Patient
    {
        // Upload baru
        if ($photo) {
            $this->deletePhoto($patient);
            $data['photo'] = $photo->store('patients', 'public');
        } elseif (! array_key_exists('photo', $data)) {
            // jangan timpa photo dengan null jika tidak ada upload
            unset($data['photo']);
        }

        // Hapus foto via checkbox
        if ($removePhoto && $patient->photo) {
            $this->deletePhoto($patient);
            $data['photo'] = null;
        }

        $patient->update($data);

        return $patient->fresh();
    }

    /**
     * Hapus pasien + foto storage.
     */
    public function delete(Patient $patient): void
    {
        $this->deletePhoto($patient);
        $patient->delete();
    }

    /**
     * Hapus file foto dari disk public.
     */
    public function deletePhoto(Patient $patient): void
    {
        if ($patient->photo && Storage::disk('public')->exists($patient->photo)) {
            Storage::disk('public')->delete($patient->photo);
        }
    }

    /**
     * Hitung umur detail dari birth_date -> [years, months, days] atau null.
     */
    public function getAgeDetail(Patient $patient): ?array
    {
        return $patient->age_detailed;
    }
}
