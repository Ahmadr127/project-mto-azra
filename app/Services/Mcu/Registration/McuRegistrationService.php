<?php

namespace App\Services\Mcu\Registration;

use App\Models\McuPackage;
use App\Models\McuRegistration;
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
            $query->whereHas('patient', fn($q) => $q->where('name','like',"%{$search}%")->orWhere('patient_code','like',"%{$search}%"));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        return $query->latest()->paginate(10)->withQueryString();
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
