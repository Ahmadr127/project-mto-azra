<?php

namespace App\Services\Mcu\Resume;

use App\Models\McuRegistration;
use App\Support\DateRangeHelper;
use App\Support\SearchHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Response;

class McuResumeService
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
        if ($request->filled('filter_patient')) {
            $v = $request->filter_patient;
            $query->whereHas('patient', function ($q) use ($v) {
                SearchHelper::whereLike($q, 'name', $v, 'and');
                SearchHelper::whereLike($q, 'patient_code', $v, 'or');
            });
        }
        // Date range dinamis default 30 days (ganti ke '60 days','90 days','2 months' etc tanpa env)
        $hasDate = $request->filled('filter_date_from') || $request->filled('filter_date_to');
        if ($request->filled('filter_date_from')) $query->whereDate('registration_date', '>=', $request->filter_date_from);
        if ($request->filled('filter_date_to')) $query->whereDate('registration_date', '<=', $request->filter_date_to);
        if (!$hasDate) {
            [$defFrom,$defTo] = DateRangeHelper::parse('30 days');
            $query->whereDate('registration_date', '>=', $defFrom)->whereDate('registration_date', '<=', $defTo);
        }
        return $query->whereIn('status',['in_progress','completed'])->latest()->paginate($request->input('per_page', 10))->withQueryString();
    }

    public function store(McuRegistration $registration, array $data): McuRegistration
    {
        $registration->update([
            'conclusion' => $data['conclusion'] ?? null,
            'recommendation' => $data['recommendation'] ?? null,
            'resume_by' => auth()->id(),
            'resume_date' => Carbon::now(),
            'status' => 'completed',
        ]);
        return $registration;
    }

    public function pdf(McuRegistration $registration)
    {
        $registration->load(['patient','package','examMedicalActions.medicalAction','examLabs.lab','examRadiologies.radiology','examAnamneses.anamnesis','physicalExamResult','resumeDoctor']);
        $pdf = Pdf::loadView('mcu.resume.pdf', compact('registration'))->setPaper('a4','portrait');
        $fileName = 'MCU_Resume_'.$registration->patient->patient_code.'_'.date('Ymd', strtotime($registration->registration_date)).'.pdf';
        return $pdf->stream($fileName);
    }
}
