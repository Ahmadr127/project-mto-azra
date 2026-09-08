<?php

namespace App\Http\Controllers\Mcu\Resume;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcu\Resume\StoreResumeRequest;
use App\Models\McuRegistration;
use App\Services\Mcu\Resume\McuResumeService;
use Illuminate\Http\Request;

class McuResumeController extends Controller
{
    public function __construct(protected McuResumeService $service) {}

    public function index(Request $request)
    {
        $registrations = $this->service->paginate($request);
        return view('mcu.resume.index', compact('registrations'));
    }

    public function show(McuRegistration $mcuRegistration)
    {
        $mcuRegistration->load(['patient','package','examMedicalActions.medicalAction','examLabs.lab','examRadiologies.radiology','examAnamneses.anamnesis','physicalExamResult','resumeDoctor']);
        return view('mcu.resume.show', compact('mcuRegistration'));
    }

    public function store(StoreResumeRequest $request, McuRegistration $mcuRegistration)
    {
        $this->service->store($mcuRegistration, $request->validated());
        return redirect()->route('mcu-resumes.index')->with('success','Resume Medis berhasil disimpan dan status diubah menjadi Selesai.');
    }

    public function printPdf(McuRegistration $mcuRegistration)
    {
        return $this->service->pdf($mcuRegistration);
    }
}
