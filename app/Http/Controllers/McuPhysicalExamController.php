<?php

namespace App\Http\Controllers;

use App\Models\McuPhysicalExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class McuPhysicalExamController extends Controller
{
    public function index(Request $request)
    {
        $query = McuPhysicalExam::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $mcu_physical_exams = $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
        
        return view('mcu.mcu-physical-exams.index', compact('mcu_physical_exams'));
    }

    public function create()
    {
        return view('mcu.mcu-physical-exams.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:mcu_medical_actions,code', // will fix manually or generalize
        ]);
        
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:' . (new McuPhysicalExam)->getTable() . ',code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        McuPhysicalExam::create($validated);

        return redirect()->route('mcu-physical-exams.index')->with('success', 'Data berhasil dibuat!');
    }

    public function edit(McuPhysicalExam $mcuPhysicalExam)
    {
        return view('mcu.mcu-physical-exams.edit', ['mcu_physical_exams' => $mcuPhysicalExam]);
    }

    public function update(Request $request, McuPhysicalExam $mcuPhysicalExam)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:' . (new McuPhysicalExam)->getTable() . ',code,' . $mcuPhysicalExam->id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $mcuPhysicalExam->update($validated);

        return redirect()->route('mcu-physical-exams.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(McuPhysicalExam $mcuPhysicalExam)
    {
        $mcuPhysicalExam->delete();
        return redirect()->route('mcu-physical-exams.index')->with('success', 'Data berhasil dihapus!');
    }
}