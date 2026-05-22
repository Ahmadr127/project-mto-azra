<?php

namespace App\Http\Controllers;

use App\Models\McuAnamnesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class McuAnamnesisController extends Controller
{
    public function index(Request $request)
    {
        $query = McuAnamnesis::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $mcu_anamneses = $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
        
        return view('mcu.mcu-anamneses.index', compact('mcu_anamneses'));
    }

    public function create()
    {
        return view('mcu.mcu-anamneses.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:mcu_medical_actions,code', // will fix manually or generalize
        ]);
        
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:' . (new McuAnamnesis)->getTable() . ',code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        McuAnamnesis::create($validated);

        return redirect()->route('mcu-anamneses.index')->with('success', 'Data berhasil dibuat!');
    }

    public function edit(McuAnamnesis $mcuAnamnesis)
    {
        return view('mcu.mcu-anamneses.edit', ['mcu_anamneses' => $mcuAnamnesis]);
    }

    public function update(Request $request, McuAnamnesis $mcuAnamnesis)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:' . (new McuAnamnesis)->getTable() . ',code,' . $mcuAnamnesis->id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $mcuAnamnesis->update($validated);

        return redirect()->route('mcu-anamneses.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(McuAnamnesis $mcuAnamnesis)
    {
        $mcuAnamnesis->delete();
        return redirect()->route('mcu-anamneses.index')->with('success', 'Data berhasil dihapus!');
    }
}