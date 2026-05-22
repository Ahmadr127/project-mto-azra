<?php

namespace App\Http\Controllers;

use App\Models\McuRadiology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class McuRadiologyController extends Controller
{
    public function index(Request $request)
    {
        $query = McuRadiology::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $mcu_radiologies = $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
        
        return view('mcu.mcu-radiologies.index', compact('mcu_radiologies'));
    }

    public function create()
    {
        return view('mcu.mcu-radiologies.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:mcu_medical_actions,code', // will fix manually or generalize
        ]);
        
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:' . (new McuRadiology)->getTable() . ',code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        McuRadiology::create($validated);

        return redirect()->route('mcu-radiologies.index')->with('success', 'Data berhasil dibuat!');
    }

    public function edit(McuRadiology $mcuRadiology)
    {
        return view('mcu.mcu-radiologies.edit', ['mcu_radiologies' => $mcuRadiology]);
    }

    public function update(Request $request, McuRadiology $mcuRadiology)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:' . (new McuRadiology)->getTable() . ',code,' . $mcuRadiology->id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $mcuRadiology->update($validated);

        return redirect()->route('mcu-radiologies.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(McuRadiology $mcuRadiology)
    {
        $mcuRadiology->delete();
        return redirect()->route('mcu-radiologies.index')->with('success', 'Data berhasil dihapus!');
    }
}