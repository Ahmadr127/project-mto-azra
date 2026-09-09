<?php

namespace App\Http\Controllers;

use App\Filters\Patient\PatientFilter;
use App\Http\Requests\Patient\PatientStoreRequest;
use App\Http\Requests\Patient\PatientUpdateRequest;
use App\Models\McuPackage;
use App\Models\Patient;
use App\Services\Patient\PatientService;
use App\Support\SearchHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    public function __construct(protected PatientService $patientService) {}
    public function index(Request $request, PatientFilter $filter)
    {
        $query = $filter->apply(Patient::query());
        $patients = $query->latest()->paginate($filter->perPage())->withQueryString();

        return view('mcu.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('mcu.patients.create');
    }

    public function store(PatientStoreRequest $request)
    {
        $validated = $request->validated();

        $this->patientService->create($validated, $request->file('photo'));

        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil ditambahkan.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['mcuRegistrations.package', 'mcuRegistrations.examLabs', 'mcuRegistrations.examRadiologies']);

        // For MCU registration modal
        $patients = Patient::orderBy('name')->get();
        $packages = McuPackage::with('items.item')->where('status', true)->orderBy('name')->get();

        return view('mcu.patients.show', compact('patient', 'patients', 'packages'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|max:255',
        ]);

        $q = trim($request->input('q'));

        // exact first (case-insensitive), then like
        $patient = Patient::whereRaw('LOWER(patient_code) = ?', [strtolower($q)])->first();
        if (! $patient) {
            $patient = Patient::where(function ($query) use ($q) {
                SearchHelper::whereLike($query, 'patient_code', $q, 'and');
                SearchHelper::whereLike($query, 'nik', $q, 'or');
            })->first();
        }

        if ($patient) {
            return redirect()->route('patients.show', $patient);
        }

        return redirect()->route('patients.index')->with('error', "Pasien dengan No. RM / NIK \"{$q}\" tidak ditemukan.");
    }

    public function edit(Patient $patient)
    {
        return view('mcu.patients.edit', compact('patient'));
    }

    public function update(PatientUpdateRequest $request, Patient $patient)
    {
        $validated = $request->validated();

        $this->patientService->update(
            $patient,
            $validated,
            $request->file('photo'),
            $request->boolean('remove_photo')
        );

        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        $this->patientService->delete($patient);

        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil dihapus.');
    }
}
