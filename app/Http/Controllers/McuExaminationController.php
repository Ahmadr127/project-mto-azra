<?php

namespace App\Http\Controllers;

use App\Models\McuRegistration;
use Illuminate\Http\Request;

class McuExaminationController extends Controller
{
    // Common method to list patients waiting for exams
    private function getRegistrations()
    {
        return McuRegistration::with(['patient', 'package'])
            ->whereIn('status', ['registered', 'in_progress'])
            ->orderBy('registration_date', 'asc')
            ->get();
    }

    // --- LAB ---
    public function labIndex()
    {
        $registrations = $this->getRegistrations();
        return view('mcu.examinations.lab.index', compact('registrations'));
    }

    public function labForm(McuRegistration $mcuRegistration)
    {
        $mcuRegistration->load('examLabs.lab');
        return view('mcu.examinations.lab.form', compact('mcuRegistration'));
    }

    public function labStore(Request $request, McuRegistration $mcuRegistration)
    {
        // $request->results is an array like [ examLabId => result_value ]
        if ($request->has('results')) {
            foreach ($request->results as $examId => $value) {
                $examLab = $mcuRegistration->examLabs()->find($examId);
                if ($examLab) {
                    $examLab->update([
                        'result_value' => $value,
                        'status' => 'completed'
                    ]);
                }
            }
        }
        
        $mcuRegistration->update(['status' => 'in_progress']);
        return redirect()->route('mcu-examinations.lab.index')->with('success', 'Hasil Lab berhasil disimpan.');
    }

    // --- RADIOLOGY ---
    public function radiologyIndex()
    {
        $registrations = $this->getRegistrations();
        return view('mcu.examinations.radiology.index', compact('registrations'));
    }

    public function radiologyForm(McuRegistration $mcuRegistration)
    {
        $mcuRegistration->load('examRadiologies.radiology');
        return view('mcu.examinations.radiology.form', compact('mcuRegistration'));
    }

    public function radiologyStore(Request $request, McuRegistration $mcuRegistration)
    {
        if ($request->has('results')) {
            foreach ($request->results as $examId => $value) {
                $exam = $mcuRegistration->examRadiologies()->find($examId);
                if ($exam) {
                    $exam->update([
                        'result' => $value,
                        'status' => 'completed'
                    ]);
                }
            }
        }
        $mcuRegistration->update(['status' => 'in_progress']);
        return redirect()->route('mcu-examinations.radiology.index')->with('success', 'Hasil Radiologi berhasil disimpan.');
    }
    
    // --- ANAMNESIS ---
    public function anamnesisIndex()
    {
        $registrations = $this->getRegistrations();
        return view('mcu.examinations.anamnesis.index', compact('registrations'));
    }

    public function anamnesisForm(McuRegistration $mcuRegistration)
    {
        $mcuRegistration->load('examAnamneses.anamnesis');
        return view('mcu.examinations.anamnesis.form', compact('mcuRegistration'));
    }

    public function anamnesisStore(Request $request, McuRegistration $mcuRegistration)
    {
        if ($request->has('results')) {
            foreach ($request->results as $examId => $value) {
                $exam = $mcuRegistration->examAnamneses()->find($examId);
                if ($exam) {
                    $exam->update([
                        'result' => $value,
                        'status' => 'completed'
                    ]);
                }
            }
        }
        $mcuRegistration->update(['status' => 'in_progress']);
        return redirect()->route('mcu-examinations.anamnesis.index')->with('success', 'Hasil Anamnesis berhasil disimpan.');
    }

    // --- PHYSICAL EXAM ---
    public function physicalIndex()
    {
        $registrations = $this->getRegistrations();
        return view('mcu.examinations.physical.index', compact('registrations'));
    }

    public function physicalForm(McuRegistration $mcuRegistration)
    {
        $mcuRegistration->load('physicalExamResult');
        return view('mcu.examinations.physical.form', compact('mcuRegistration'));
    }

    public function physicalStore(Request $request, McuRegistration $mcuRegistration)
    {
        $result = $mcuRegistration->physicalExamResult;
        if ($result) {
            $result->update([
                'vital_signs' => $request->vital_signs,
                'head_and_neck' => $request->head_and_neck,
                'thorax' => $request->thorax,
                'abdomen' => $request->abdomen,
                'urogenital' => $request->urogenital,
                'extremities' => $request->extremities,
                'others' => $request->others,
                'doctor_id' => auth()->id(),
                'status' => 'completed'
            ]);
        }
        
        $mcuRegistration->update(['status' => 'in_progress']);
        return redirect()->route('mcu-examinations.physical.index')->with('success', 'Hasil Pemeriksaan Fisik berhasil disimpan.');
    }
    
    // --- MEDICAL ACTION (KONSULTASI DOKTER) ---
    public function doctorIndex()
    {
        $registrations = $this->getRegistrations();
        return view('mcu.examinations.doctor.index', compact('registrations'));
    }

    public function doctorForm(McuRegistration $mcuRegistration)
    {
        $mcuRegistration->load('examMedicalActions.medicalAction');
        return view('mcu.examinations.doctor.form', compact('mcuRegistration'));
    }

    public function doctorStore(Request $request, McuRegistration $mcuRegistration)
    {
        if ($request->has('results')) {
            foreach ($request->results as $examId => $value) {
                $exam = $mcuRegistration->examMedicalActions()->find($examId);
                if ($exam) {
                    $exam->update([
                        'result' => $value,
                        'doctor_id' => auth()->id(), // the logged-in doctor
                        'status' => 'completed'
                    ]);
                }
            }
        }
        $mcuRegistration->update(['status' => 'in_progress']); // Or completed if everything is done, but keep simple for now
        return redirect()->route('mcu-examinations.doctor.index')->with('success', 'Hasil Tindakan berhasil disimpan.');
    }
}