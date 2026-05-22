<?php

namespace App\Http\Controllers;

use App\Models\McuRegistration;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class McuResumeController extends Controller
{
    public function index(Request $request)
    {
        $query = McuRegistration::with(['patient', 'package']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('name', 'like', "%{\$search}%")
                  ->orWhere('patient_code', 'like', "%{\$search}%");
            });
        }
        
        // Show in_progress or completed
        $registrations = $query->whereIn('status', ['in_progress', 'completed'])
                               ->latest()
                               ->paginate(10)
                               ->withQueryString();
                               
        return view('mcu.resume.index', compact('registrations'));
    }

    public function show(McuRegistration $mcuRegistration)
    {
        $mcuRegistration->load([
            'patient', 'package',
            'examMedicalActions.medicalAction',
            'examLabs.lab',
            'examRadiologies.radiology',
            'examAnamneses.anamnesis',
            'physicalExamResult',
            'resumeDoctor'
        ]);

        return view('mcu.resume.show', compact('mcuRegistration'));
    }

    public function store(Request $request, McuRegistration $mcuRegistration)
    {
        $request->validate([
            'conclusion' => 'nullable|string',
            'recommendation' => 'nullable|string'
        ]);

        $mcuRegistration->update([
            'conclusion' => $request->conclusion,
            'recommendation' => $request->recommendation,
            'resume_by' => auth()->id(),
            'resume_date' => Carbon::now(),
            'status' => 'completed'
        ]);

        return redirect()->route('mcu-resumes.index')->with('success', 'Resume Medis berhasil disimpan dan status diubah menjadi Selesai.');
    }

    public function printPdf(McuRegistration $mcuRegistration)
    {
        $mcuRegistration->load([
            'patient', 'package',
            'examMedicalActions.medicalAction',
            'examLabs.lab',
            'examRadiologies.radiology',
            'examAnamneses.anamnesis',
            'physicalExamResult',
            'resumeDoctor'
        ]);

        $pdf = Pdf::loadView('mcu.resume.pdf', compact('mcuRegistration'))
                  ->setPaper('a4', 'portrait');

        $fileName = 'MCU_Resume_' . $mcuRegistration->patient->patient_code . '_' . date('Ymd', strtotime($mcuRegistration->registration_date)) . '.pdf';
        
        return $pdf->stream($fileName);
    }
}
