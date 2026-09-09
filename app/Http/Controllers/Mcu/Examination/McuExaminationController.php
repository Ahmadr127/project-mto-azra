<?php

namespace App\Http\Controllers\Mcu\Examination;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcu\Examination\StoreAnamnesisRequest;
use App\Http\Requests\Mcu\Examination\StoreDoctorRequest;
use App\Http\Requests\Mcu\Examination\StoreLabRequest;
use App\Http\Requests\Mcu\Examination\StorePhysicalRequest;
use App\Http\Requests\Mcu\Examination\StoreRadiologyRequest;
use App\Models\McuRegistration;
use App\Services\Mcu\Examination\McuExaminationService;
use Illuminate\Http\Request;

class McuExaminationController extends Controller
{
    public function __construct(protected McuExaminationService $service) {}

    private function getRegistrations(Request $request = null)
    {
        if ($request) {
            return $this->service->paginate($request);
        }
        return $this->service->getRegistrations();
    }

    // LAB
    public function labIndex(Request $request) { $registrations = $this->service->paginate($request); return view('mcu.examinations.lab.index', compact('registrations')); }
    public function labForm(McuRegistration $mcuRegistration) { $mcuRegistration->load('examLabs.lab'); return view('mcu.examinations.lab.form', compact('mcuRegistration')); }
    public function labStore(StoreLabRequest $request, McuRegistration $mcuRegistration) { $this->service->storeLab($mcuRegistration, $request->input('results', [])); return redirect()->route('mcu-examinations.lab.index')->with('success','Hasil Lab berhasil disimpan.'); }

    // RADIOLOGY
    public function radiologyIndex(Request $request) { $registrations = $this->service->paginate($request); return view('mcu.examinations.radiology.index', compact('registrations')); }
    public function radiologyForm(McuRegistration $mcuRegistration) { $mcuRegistration->load('examRadiologies.radiology'); return view('mcu.examinations.radiology.form', compact('mcuRegistration')); }
    public function radiologyStore(StoreRadiologyRequest $request, McuRegistration $mcuRegistration) { $this->service->storeRadiology($mcuRegistration, $request->input('results', []), $request->input('is_normal', [])); return redirect()->route('mcu-examinations.radiology.index')->with('success','Hasil Penunjang Non-Lab berhasil disimpan.'); }

    // ANAMNESIS
    public function anamnesisIndex(Request $request) { $registrations = $this->service->paginate($request); return view('mcu.examinations.anamnesis.index', compact('registrations')); }
    public function anamnesisForm(McuRegistration $mcuRegistration) { $mcuRegistration->load('examAnamneses.anamnesis'); return view('mcu.examinations.anamnesis.form', compact('mcuRegistration')); }
    public function anamnesisStore(StoreAnamnesisRequest $request, McuRegistration $mcuRegistration) { $this->service->storeAnamnesis($mcuRegistration, $request->input('results', [])); return redirect()->route('mcu-examinations.anamnesis.index')->with('success','Hasil Anamnesis berhasil disimpan.'); }

    // PHYSICAL
    public function physicalIndex(Request $request) { $registrations = $this->service->paginate($request); return view('mcu.examinations.physical.index', compact('registrations')); }
    public function physicalForm(McuRegistration $mcuRegistration) { $mcuRegistration->load('physicalExamResult'); return view('mcu.examinations.physical.form', compact('mcuRegistration')); }
    public function physicalStore(StorePhysicalRequest $request, McuRegistration $mcuRegistration) { $this->service->storePhysical($mcuRegistration, $request->validated()); return redirect()->route('mcu-examinations.physical.index')->with('success','Hasil Pemeriksaan Fisik berhasil disimpan.'); }

    // DOCTOR
    public function doctorIndex(Request $request) { $registrations = $this->service->paginate($request); return view('mcu.examinations.doctor.index', compact('registrations')); }
    public function doctorForm(McuRegistration $mcuRegistration) { $mcuRegistration->load('examMedicalActions.medicalAction'); return view('mcu.examinations.doctor.form', compact('mcuRegistration')); }
    public function doctorStore(StoreDoctorRequest $request, McuRegistration $mcuRegistration) { $this->service->storeDoctor($mcuRegistration, $request->input('results', [])); return redirect()->route('mcu-examinations.doctor.index')->with('success','Hasil Tindakan berhasil disimpan.'); }
}
