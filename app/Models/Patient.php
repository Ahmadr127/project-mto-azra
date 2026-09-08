<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_code', 'nik', 'name', 'gender',
        'birth_place', 'birth_date', 'age',
        'address', 'kelurahan', 'kecamatan', 'kabupaten_kota', 'provinsi', 'kode_pos',
        'phone', 'emergency_phone', 'photo',
        'company', 'department', 'employee_status', 'bpjs',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected $appends = ['age_formatted', 'photo_url'];

    public function mcuRegistrations()
    {
        return $this->hasMany(McuRegistration::class);
    }

    public function getAgeDetailedAttribute(): ?array
    {
        if (! $this->birth_date) {
            return null;
        }
        $birth = Carbon::parse($this->birth_date);
        $now = Carbon::now();
        if ($birth->isFuture()) {
            return null;
        }
        $diff = $birth->diff($now);

        return [
            'years' => $diff->y,
            'months' => $diff->m,
            'days' => $diff->d,
        ];
    }

    public function getAgeFormattedAttribute(): string
    {
        $detail = $this->age_detailed;
        if (! $detail) {
            return '-';
        }

        $parts = [];
        if ($detail['years'] > 0) {
            $parts[] = $detail['years'].' tahun';
        }
        if ($detail['months'] > 0) {
            $parts[] = $detail['months'].' bulan';
        }
        if ($detail['days'] > 0) {
            $parts[] = $detail['days'].' hari';
        }

        return $parts ? implode(' ', $parts) : '0 hari';
    }

    public function getAgeYearsAttribute(): ?int
    {
        return $this->age_detailed['years'] ?? null;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return Storage::url($this->photo);
        }

        return null;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->kelurahan ? 'Kel. '.$this->kelurahan : null,
            $this->kecamatan ? 'Kec. '.$this->kecamatan : null,
            $this->kabupaten_kota,
            $this->provinsi,
            $this->kode_pos ? 'Kode Pos '.$this->kode_pos : null,
        ]);

        return $parts ? implode(', ', $parts) : '-';
    }
}
