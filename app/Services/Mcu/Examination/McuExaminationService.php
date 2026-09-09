<?php

namespace App\Services\Mcu\Examination;

use App\Models\McuRegistration;
use App\Support\DateRangeHelper;
use App\Support\SearchHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class McuExaminationService
{
    public function getRegistrations(): Collection
    {
        return McuRegistration::with(['patient','package'])
            ->whereIn('status', ['registered','in_progress'])
            ->orderBy('registration_date','asc')
            ->get();
    }

    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = McuRegistration::with(['patient','package'])
            ->whereIn('status', ['registered','in_progress']);

        // Global search fallback (legacy)
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
            $v = $request->filter_package;
            $query->whereHas('package', function ($q) use ($v) {
                SearchHelper::whereLike($q, 'name', $v);
            });
        }
        // Date range (luar table via date-range-filter) - dinamis default 30 days (ganti ke 60 days / 2 months etc tanpa env)
        $hasDate = $request->filled('filter_date_from') || $request->filled('filter_date_to');
        if ($request->filled('filter_date_from')) {
            $query->whereDate('registration_date', '>=', $request->filter_date_from);
        }
        if ($request->filled('filter_date_to')) {
            $query->whereDate('registration_date', '<=', $request->filter_date_to);
        }
        if (!$hasDate) {
            [$defFrom,$defTo] = DateRangeHelper::parse('30 days');
            $query->whereDate('registration_date', '>=', $defFrom)->whereDate('registration_date', '<=', $defTo);
        }

        return $query->orderBy('registration_date','asc')
            ->paginate($request->input('per_page', 10))
            ->withQueryString();
    }

    public function storeLab(McuRegistration $reg, array $results): void
    {
        foreach ($results as $examId => $value) {
            $exam = $reg->examLabs()->find($examId);
            if ($exam) $exam->update(['result_value'=>$value,'status'=>'completed']);
        }
        $reg->update(['status'=>'in_progress']);
    }

    public function storeRadiology(McuRegistration $reg, array $results, array $isNormal = []): void
    {
        foreach ($results as $examId => $value) {
            $exam = $reg->examRadiologies()->find($examId);
            if ($exam) {
                $isNorm = isset($isNormal[$examId]) && $isNormal[$examId]==1;
                $exam->update([
                    'result'=> json_encode(['is_normal'=>$isNorm,'findings'=>$value?:'']),
                    'status'=>'completed'
                ]);
            }
        }
        $reg->update(['status'=>'in_progress']);
    }

    public function storeAnamnesis(McuRegistration $reg, array $results): void
    {
        foreach ($results as $examId => $value) {
            $exam = $reg->examAnamneses()->find($examId);
            if ($exam) $exam->update(['result'=>$value,'status'=>'completed']);
        }
        $reg->update(['status'=>'in_progress']);
    }

    public function storePhysical(McuRegistration $reg, array $data): void
    {
        $result = $reg->physicalExamResult;
        if ($result) {
            $result->update([
                'vital_signs'=> $data['vital_signs'] ?? null,
                'head_and_neck'=> $data['head_and_neck'] ?? null,
                'thorax'=> $data['thorax'] ?? null,
                'abdomen'=> $data['abdomen'] ?? null,
                'urogenital'=> $data['urogenital'] ?? null,
                'extremities'=> $data['extremities'] ?? null,
                'others'=> $data['others'] ?? null,
                'doctor_id'=> auth()->id(),
                'status'=>'completed',
            ]);
        }
        $reg->update(['status'=>'in_progress']);
    }

    public function storeDoctor(McuRegistration $reg, array $results): void
    {
        foreach ($results as $examId => $value) {
            $exam = $reg->examMedicalActions()->find($examId);
            if ($exam) $exam->update(['result'=>$value,'doctor_id'=>auth()->id(),'status'=>'completed']);
        }
        $reg->update(['status'=>'in_progress']);
    }
}
