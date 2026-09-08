<?php

namespace App\Http\Controllers;

use App\Models\McuPackage;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('patient_code', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }
        // Optional per-column filters (if data-table reused)
        foreach (['patient_code', 'nik', 'name'] as $field) {
            if ($request->filled('filter_'.$field)) {
                $query->where($field, 'like', '%'.$request->input('filter_'.$field).'%');
            }
        }
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $patients = $query->latest()->paginate($perPage)->withQueryString();

        return view('mcu.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('mcu.patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_code' => 'required|string|unique:patients,patient_code',
            'nik' => 'nullable|string|max:16',
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'address' => 'nullable|string|max:1000',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'emergency_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'department' => 'nullable|string|max:255',
            'employee_status' => 'nullable|string|max:255',
            'bpjs' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('patients', 'public');
        }

        // keep legacy company not used; ignore
        Patient::create($validated);

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

        // exact first, then like
        $patient = Patient::where('patient_code', $q)->first();
        if (! $patient) {
            $patient = Patient::where('patient_code', 'like', "%{$q}%")
                ->orWhere('nik', 'like', "%{$q}%")
                ->first();
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

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'patient_code' => 'required|string|unique:patients,patient_code,'.$patient->id,
            'nik' => 'nullable|string|max:16',
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'address' => 'nullable|string|max:1000',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'emergency_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'department' => 'nullable|string|max:255',
            'employee_status' => 'nullable|string|max:255',
            'bpjs' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('photo')) {
            // delete old
            if ($patient->photo && Storage::disk('public')->exists($patient->photo)) {
                Storage::disk('public')->delete($patient->photo);
            }
            $validated['photo'] = $request->file('photo')->store('patients', 'public');
        } else {
            unset($validated['photo']);
        }

        // handle remove photo checkbox
        if ($request->boolean('remove_photo') && $patient->photo) {
            Storage::disk('public')->delete($patient->photo);
            $validated['photo'] = null;
        }

        $patient->update($validated);

        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        if ($patient->photo && Storage::disk('public')->exists($patient->photo)) {
            Storage::disk('public')->delete($patient->photo);
        }
        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil dihapus.');
    }
}
