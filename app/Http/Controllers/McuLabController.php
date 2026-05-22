<?php

namespace App\Http\Controllers;

use App\Models\McuLab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class McuLabController extends Controller
{
    public function index(Request $request)
    {
        $query = McuLab::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $mcu_labs = $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
        
        return view('mcu.mcu-labs.index', compact('mcu_labs'));
    }

    public function create()
    {
        return view('mcu.mcu-labs.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:mcu_medical_actions,code', // will fix manually or generalize
        ]);
        
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:' . (new McuLab)->getTable() . ',code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        McuLab::create($validated);

        return redirect()->route('mcu-labs.index')->with('success', 'Data berhasil dibuat!');
    }

    public function edit(McuLab $mcuLab)
    {
        return view('mcu.mcu-labs.edit', ['mcu_labs' => $mcuLab]);
    }

    public function update(Request $request, McuLab $mcuLab)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:' . (new McuLab)->getTable() . ',code,' . $mcuLab->id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $mcuLab->update($validated);

        return redirect()->route('mcu-labs.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(McuLab $mcuLab)
    {
        $mcuLab->delete();
        return redirect()->route('mcu-labs.index')->with('success', 'Data berhasil dihapus!');
    }
}