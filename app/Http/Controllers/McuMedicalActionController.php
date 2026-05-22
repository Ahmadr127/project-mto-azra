<?php

namespace App\Http\Controllers;

use App\Models\McuMedicalAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class McuMedicalActionController extends Controller
{
    public function index(Request $request)
    {
        $query = McuMedicalAction::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $mcu_medical_actions = $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
        
        return view('mcu.mcu-medical-actions.index', compact('mcu_medical_actions'));
    }

    public function create()
    {
        return view('mcu.mcu-medical-actions.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:mcu_medical_actions,code', // will fix manually or generalize
        ]);
        
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:' . (new McuMedicalAction)->getTable() . ',code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        McuMedicalAction::create($validated);

        return redirect()->route('mcu-medical-actions.index')->with('success', 'Data berhasil dibuat!');
    }

    public function edit(McuMedicalAction $mcuMedicalAction)
    {
        return view('mcu.mcu-medical-actions.edit', ['mcu_medical_actions' => $mcuMedicalAction]);
    }

    public function update(Request $request, McuMedicalAction $mcuMedicalAction)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:' . (new McuMedicalAction)->getTable() . ',code,' . $mcuMedicalAction->id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $mcuMedicalAction->update($validated);

        return redirect()->route('mcu-medical-actions.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(McuMedicalAction $mcuMedicalAction)
    {
        $mcuMedicalAction->delete();
        return redirect()->route('mcu-medical-actions.index')->with('success', 'Data berhasil dihapus!');
    }
}