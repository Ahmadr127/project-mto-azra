<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('patient_code', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }
        $patients = $query->latest()->paginate(10)->withQueryString();
        return view('mcu.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('mcu.patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_code' => 'required|string|unique:patients',
            'nik' => 'nullable|string|max:16',
            'name' => 'required|string',
            'gender' => 'nullable|in:L,P',
            'birth_date' => 'nullable|date',
            'age' => 'nullable|integer',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'department' => 'nullable|string',
            'employee_status' => 'nullable|string',
            'bpjs' => 'nullable|string',
        ]);

        Patient::create($validated);
        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil ditambahkan.');
    }

    public function edit(Patient $patient)
    {
        return view('mcu.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'patient_code' => 'required|string|unique:patients,patient_code,' . $patient->id,
            'nik' => 'nullable|string|max:16',
            'name' => 'required|string',
            'gender' => 'nullable|in:L,P',
            'birth_date' => 'nullable|date',
            'age' => 'nullable|integer',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
            'department' => 'nullable|string',
            'employee_status' => 'nullable|string',
            'bpjs' => 'nullable|string',
        ]);

        $patient->update($validated);
        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Data Pasien berhasil dihapus.');
    }
}