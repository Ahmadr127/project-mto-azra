<?php

namespace App\Services\Mcu\Resume;

use App\Models\McuRegistration;
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
            $query->whereHas('patient', fn($q) => $q->where('name','like',"%{$search}%")->orWhere('patient_code','like',"%{$search}%"));
        }
        return $query->whereIn('status',['in_progress','completed'])->latest()->paginate(10)->withQueryString();
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
