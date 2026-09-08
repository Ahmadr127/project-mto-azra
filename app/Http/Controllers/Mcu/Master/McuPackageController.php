<?php

namespace App\Http\Controllers\Mcu\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcu\Package\StoreMcuPackageRequest;
use App\Http\Requests\Mcu\Package\UpdateMcuPackageRequest;
use App\Models\McuAnamnesis;
use App\Models\McuLab;
use App\Models\McuMedicalAction;
use App\Models\McuPackage;
use App\Models\McuPhysicalExam;
use App\Models\McuRadiology;
use App\Services\Mcu\Package\McuPackageService;
use Illuminate\Http\Request;

class McuPackageController extends Controller
{
    public function __construct(protected McuPackageService $service) {}

    public function index(Request $request)
    {
        $mcu_packages = $this->service->paginate($request);
        return view('mcu.mcu-packages.index', compact('mcu_packages'));
    }

    public function create()
    {
        $medicalActions = McuMedicalAction::where('status', true)->orderBy('display_order')->get();
        $labs = McuLab::where('status', true)->orderBy('display_order')->get();
        $radiologies = McuRadiology::where('status', true)->orderBy('display_order')->get();
        $anamneses = McuAnamnesis::where('status', true)->orderBy('display_order')->get();
        $physicalExams = McuPhysicalExam::where('status', true)->orderBy('display_order')->get();
        return view('mcu.mcu-packages.create', compact('medicalActions','labs','radiologies','anamneses','physicalExams'));
    }

    public function store(StoreMcuPackageRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('mcu-packages.index')->with('success','Paket berhasil dibuat!');
    }

    public function edit(McuPackage $mcuPackage)
    {
        $medicalActions = McuMedicalAction::where('status', true)->orderBy('display_order')->get();
        $labs = McuLab::where('status', true)->orderBy('display_order')->get();
        $radiologies = McuRadiology::where('status', true)->orderBy('display_order')->get();
        $anamneses = McuAnamnesis::where('status', true)->orderBy('display_order')->get();
        $physicalExams = McuPhysicalExam::where('status', true)->orderBy('display_order')->get();
        $selectedItems = $mcuPackage->items->map(fn($item) => class_basename($item->item_type).':'.$item->item_id)->toArray();
        return view('mcu.mcu-packages.edit', compact('mcuPackage','medicalActions','labs','radiologies','anamneses','physicalExams','selectedItems'));
    }

    public function update(UpdateMcuPackageRequest $request, McuPackage $mcuPackage)
    {
        $this->service->update($mcuPackage, $request->validated());
        return redirect()->route('mcu-packages.index')->with('success','Paket berhasil diperbarui!');
    }

    public function destroy(McuPackage $mcuPackage)
    {
        $this->service->delete($mcuPackage);
        return redirect()->route('mcu-packages.index')->with('success','Paket berhasil dihapus!');
    }
}
