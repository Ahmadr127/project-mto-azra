<?php

namespace App\Services\Mcu\Registration;

use App\Models\McuPackage;
use App\Models\McuRegistration;
use App\Support\DateRangeHelper;
use App\Support\SearchHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class McuRegistrationService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = McuRegistration::with(['patient','package']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                SearchHelper::whereLike($q, 'name', $search, 'and');
                SearchHelper::whereLike($q, 'patient_code', $search, 'or');
            });
        }
        // Per-column filters (data-table) - case-insensitive
        if ($request->filled('filter_patient')) {
            $v = $request->filter_patient;
            $query->whereHas('patient', function ($q) use ($v) {
                SearchHelper::whereLike($q, 'name', $v, 'and');
                SearchHelper::whereLike($q, 'patient_code', $v, 'or');
            });
        }
        if ($request->filled('filter_package')) {
            $query->whereHas('package', function ($q) use ($request) {
                SearchHelper::whereLike($q, 'name', $request->filter_package);
            });
        }
        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Date range filter (luar table via date-range-filter) - dinamis, default otomatis
        // Pilihan dinamis: '7 days','30 days','60 days','90 days','1 month','2 months','3 months','6 months','1 year'
        // Ganti '30 days' di bawah ke '60 days' / '90 days' etc untuk ubah global tanpa env
        // Semua view yang pakai <x-date-range-filter /> tanpa prop otomatis ikut default ini
        $hasDateFilter = $request->filled('filter_date_from') || $request->filled('filter_date_to');
        if ($request->filled('filter_date_from')) {
            $query->whereDate('registration_date', '>=', $request->filter_date_from);
        }
        if ($request->filled('filter_date_to')) {
            $query->whereDate('registration_date', '<=', $request->filter_date_to);
        }
        if (!$hasDateFilter) {
            // Default otomatis: 30 days terakhir (dinamis, ganti ke '60 days' / '2 months' / etc)
            [$defFrom, $defTo] = DateRangeHelper::parse('30 days');
            $query->whereDate('registration_date', '>=', $defFrom)->whereDate('registration_date', '<=', $defTo);
        }
        // legacy per-column exact date (single)
        if ($request->filled('filter_registration_date')) {
            $query->whereDate('registration_date', $request->filter_registration_date);
        }
        return $query->latest()->paginate($request->input('per_page', 10))->withQueryString();
    }

    public function create(array $data): McuRegistration
    {
        return DB::transaction(function () use ($data) {
            $registration = McuRegistration::create([
                'patient_id' => $data['patient_id'],
                'mcu_package_id' => $data['mcu_package_id'],
                'registration_date' => $data['registration_date'],
                'status' => 'registered',
            ]);

            $package = McuPackage::with('items')->find($data['mcu_package_id']);
            foreach ($package->items as $item) {
                $type = class_basename($item->item_type);
                switch ($type) {
                    case 'McuLab':
                        $labMaster = \App\Models\McuLab::find($item->item_id);
                        $registration->examLabs()->create([
                            'mcu_lab_id' => $item->item_id,
                            'normal_value' => $labMaster?->normal_value,
                            'status' => 'pending',
                        ]);
                        break;
                    case 'McuMedicalAction':
                        $registration->examMedicalActions()->create(['mcu_medical_action_id' => $item->item_id, 'status' => 'pending']);
                        break;
                    case 'McuRadiology':
                        $registration->examRadiologies()->create(['mcu_radiology_id' => $item->item_id, 'status' => 'pending']);
                        break;
                    case 'McuAnamnesis':
                        $registration->examAnamneses()->create(['mcu_anamnesis_id' => $item->item_id, 'status' => 'pending']);
                        break;
                    case 'McuPhysicalExam':
                        if (! $registration->relationLoaded('physicalExamResult') || ! $registration->physicalExamResult) {
                            // Use fresh check to avoid cached null
                            if (! $registration->fresh()->physicalExamResult) {
                                $registration->physicalExamResult()->create(['status' => 'pending']);
                            }
                        }
                        break;
                }
            }
            return $registration->fresh()->load(['examLabs','examRadiologies','examAnamneses','examMedicalActions','physicalExamResult']);
        });
    }

    public function update(McuRegistration $registration, array $data): McuRegistration
    {
        $registration->update($data);
        return $registration;
    }

    public function delete(McuRegistration $registration): void
    {
        $registration->delete();
    }
}
