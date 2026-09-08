<?php

namespace App\Http\Controllers;

use App\Models\McuPackage;
use App\Models\McuRegistration;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class McuRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = McuRegistration::with(['patient', 'package']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('patient_code', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $registrations = $query->latest()->paginate(10)->withQueryString();

        // For modal form
        $patients = Patient::orderBy('name')->get();
        $packages = McuPackage::with('items.item')->where('status', true)->orderBy('name')->get();

        return view('mcu.registrations.index', compact('registrations', 'patients', 'packages'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('name')->get();
        $packages = McuPackage::with('items.item')->where('status', true)->orderBy('name')->get();
        $selectedPatientId = $request->query('patient_id');

        return view('mcu.registrations.create', compact('patients', 'packages', 'selectedPatientId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'mcu_package_id' => 'required|exists:mcu_packages,id',
            'registration_date' => 'required|date',
        ]);

        DB::transaction(function () use ($validated) {
            // Create Registration
            $registration = McuRegistration::create([
                'patient_id' => $validated['patient_id'],
                'mcu_package_id' => $validated['mcu_package_id'],
                'registration_date' => $validated['registration_date'],
                'status' => 'registered',
            ]);

            // Auto-generate items based on package
            $package = McuPackage::with('items')->find($validated['mcu_package_id']);

            foreach ($package->items as $item) {
                $type = class_basename($item->item_type); // e.g., McuLab, McuRadiology

                switch ($type) {
                    case 'McuLab':
                        $labMaster = \App\Models\McuLab::find($item->item_id);
                        $registration->examLabs()->create([
                            'mcu_lab_id' => $item->item_id,
                            'normal_value' => $labMaster ? $labMaster->normal_value : null,
                            'status' => 'pending',
                        ]);
                        break;
                    case 'McuMedicalAction':
                        $registration->examMedicalActions()->create([
                            'mcu_medical_action_id' => $item->item_id,
                            'status' => 'pending',
                        ]);
                        break;
                    case 'McuRadiology':
                        $registration->examRadiologies()->create([
                            'mcu_radiology_id' => $item->item_id,
                            'status' => 'pending',
                        ]);
                        break;
                    case 'McuAnamnesis':
                        $registration->examAnamneses()->create([
                            'mcu_anamnesis_id' => $item->item_id,
                            'status' => 'pending',
                        ]);
                        break;
                    case 'McuPhysicalExam':
                        if (! $registration->physicalExamResult) {
                            $registration->physicalExamResult()->create([
                                'status' => 'pending',
                            ]);
                        }
                        break;
                }
            }
        });

        return redirect()->route('mcu-registrations.index')->with('success', 'Registrasi MCU berhasil dibuat dan item pemeriksaan telah disiapkan.');
    }

    public function show(McuRegistration $mcuRegistration)
    {
        $mcuRegistration->load([
            'patient',
            'package',
            'examLabs.lab',
            'examMedicalActions.medicalAction',
            'examRadiologies.radiology',
            'examAnamneses.anamnesis',
            'physicalExamResult',
        ]);

        return view('mcu.registrations.show', compact('mcuRegistration'));
    }

    public function edit(McuRegistration $mcuRegistration)
    {
        $patients = Patient::orderBy('name')->get();
        $packages = McuPackage::where('status', true)->orderBy('name')->get();

        return view('mcu.registrations.edit', compact('mcuRegistration', 'patients', 'packages'));
    }

    public function update(Request $request, McuRegistration $mcuRegistration)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'mcu_package_id' => 'required|exists:mcu_packages,id',
            'registration_date' => 'required|date',
            'status' => 'required|in:registered,in_progress,completed,cancelled',
        ]);

        // If package changed, we might need to recreate items, but for now we just update basic info.
        // Handling package change logic can be complex (what if exams already have results?), so usually it's restricted or handled carefully.
        $mcuRegistration->update($validated);

        return redirect()->route('mcu-registrations.index')->with('success', 'Registrasi MCU berhasil diperbarui.');
    }

    public function destroy(McuRegistration $mcuRegistration)
    {
        $mcuRegistration->delete();

        return redirect()->route('mcu-registrations.index')->with('success', 'Registrasi berhasil dihapus.');
    }
}
