<?php

namespace App\Http\Controllers;

use App\Models\McuPackage;
use App\Models\McuMedicalAction;
use App\Models\McuLab;
use App\Models\McuRadiology;
use App\Models\McuAnamnesis;
use App\Models\McuPhysicalExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class McuPackageController extends Controller
{
    public function index(Request $request)
    {
        $query = McuPackage::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        $mcu_packages = $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
        
        return view('mcu.mcu-packages.index', compact('mcu_packages'));
    }

    public function create()
    {
        $medicalActions = McuMedicalAction::where('status', true)->orderBy('display_order')->get();
        $labs = McuLab::where('status', true)->orderBy('display_order')->get();
        $radiologies = McuRadiology::where('status', true)->orderBy('display_order')->get();
        $anamneses = McuAnamnesis::where('status', true)->orderBy('display_order')->get();
        $physicalExams = McuPhysicalExam::where('status', true)->orderBy('display_order')->get();

        return view('mcu.mcu-packages.create', compact('medicalActions', 'labs', 'radiologies', 'anamneses', 'physicalExams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:mcu_packages,code',
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'items' => 'nullable|array',
            'items.*' => 'string' // Format: ModelName:ID (e.g., McuLab:1)
        ]);

        DB::transaction(function() use ($validated) {
            $package = McuPackage::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'base_price' => $validated['base_price'],
                'description' => $validated['description'],
                'display_order' => $validated['display_order'],
                'status' => $validated['status'],
            ]);

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    list($type, $id) = explode(':', $item);
                    $modelClass = "App\\Models\\" . $type;
                    if (class_exists($modelClass)) {
                        $package->items()->create([
                            'item_type' => $modelClass,
                            'item_id' => $id
                        ]);
                    }
                }
            }
        });

        return redirect()->route('mcu-packages.index')->with('success', 'Paket berhasil dibuat!');
    }

    public function edit(McuPackage $mcuPackage)
    {
        $medicalActions = McuMedicalAction::where('status', true)->orderBy('display_order')->get();
        $labs = McuLab::where('status', true)->orderBy('display_order')->get();
        $radiologies = McuRadiology::where('status', true)->orderBy('display_order')->get();
        $anamneses = McuAnamnesis::where('status', true)->orderBy('display_order')->get();
        $physicalExams = McuPhysicalExam::where('status', true)->orderBy('display_order')->get();
        
        $selectedItems = $mcuPackage->items->map(function($item) {
            return class_basename($item->item_type) . ':' . $item->item_id;
        })->toArray();

        return view('mcu.mcu-packages.edit', compact('mcuPackage', 'medicalActions', 'labs', 'radiologies', 'anamneses', 'physicalExams', 'selectedItems'));
    }

    public function update(Request $request, McuPackage $mcuPackage)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:mcu_packages,code,' . $mcuPackage->id,
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'display_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'items' => 'nullable|array',
            'items.*' => 'string'
        ]);

        DB::transaction(function() use ($validated, $mcuPackage) {
            $mcuPackage->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'base_price' => $validated['base_price'],
                'description' => $validated['description'],
                'display_order' => $validated['display_order'],
                'status' => $validated['status'],
            ]);

            // Sync items
            $mcuPackage->items()->delete();
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    list($type, $id) = explode(':', $item);
                    $modelClass = "App\\Models\\" . $type;
                    if (class_exists($modelClass)) {
                        $mcuPackage->items()->create([
                            'item_type' => $modelClass,
                            'item_id' => $id
                        ]);
                    }
                }
            }
        });

        return redirect()->route('mcu-packages.index')->with('success', 'Paket berhasil diperbarui!');
    }

    public function destroy(McuPackage $mcuPackage)
    {
        $mcuPackage->delete();
        return redirect()->route('mcu-packages.index')->with('success', 'Paket berhasil dihapus!');
    }
}