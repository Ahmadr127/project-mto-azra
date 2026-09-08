<?php

namespace App\Http\Controllers\Mcu\Registration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcu\Registration\StoreMcuRegistrationRequest;
use App\Http\Requests\Mcu\Registration\UpdateMcuRegistrationRequest;
use App\Models\McuPackage;
use App\Models\McuRegistration;
use App\Models\Patient;
use App\Services\Mcu\Registration\McuRegistrationService;
use Illuminate\Http\Request;

class McuRegistrationController extends Controller
{
    public function __construct(protected McuRegistrationService $service) {}

    public function index(Request $request)
    {
        $registrations = $this->service->paginate($request);
        $patients = Patient::orderBy('name')->get();
        $packages = McuPackage::with('items.item')->where('status', true)->orderBy('name')->get();
        return view('mcu.registrations.index', compact('registrations','patients','packages'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('name')->get();
        $packages = McuPackage::with('items.item')->where('status', true)->orderBy('name')->get();
        $selectedPatientId = $request->query('patient_id');
        return view('mcu.registrations.create', compact('patients','packages','selectedPatientId'));
    }

    public function store(StoreMcuRegistrationRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('mcu-registrations.index')->with('success','Registrasi MCU berhasil dibuat dan item pemeriksaan telah disiapkan.');
    }

    public function show(McuRegistration $mcuRegistration)
    {
        $mcuRegistration->load(['patient','package','examLabs.lab','examMedicalActions.medicalAction','examRadiologies.radiology','examAnamneses.anamnesis','physicalExamResult']);
        return view('mcu.registrations.show', compact('mcuRegistration'));
    }

    public function edit(McuRegistration $mcuRegistration)
    {
        $patients = Patient::orderBy('name')->get();
        $packages = McuPackage::where('status', true)->orderBy('name')->get();
        return view('mcu.registrations.edit', compact('mcuRegistration','patients','packages'));
    }

    public function update(UpdateMcuRegistrationRequest $request, McuRegistration $mcuRegistration)
    {
        $this->service->update($mcuRegistration, $request->validated());
        return redirect()->route('mcu-registrations.index')->with('success','Registrasi MCU berhasil diperbarui.');
    }

    public function destroy(McuRegistration $mcuRegistration)
    {
        $this->service->delete($mcuRegistration);
        return redirect()->route('mcu-registrations.index')->with('success','Registrasi berhasil dihapus.');
    }
}
