<?php

namespace App\Http\Controllers\Mcu\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcu\Master\StoreMcuMedicalActionRequest;
use App\Http\Requests\Mcu\Master\UpdateMcuMedicalActionRequest;
use App\Models\McuMedicalAction;
use App\Services\Mcu\Master\McuMedicalActionService;
use Illuminate\Http\Request;

class McuMedicalActionController extends Controller
{
    public function __construct(protected McuMedicalActionService $service) {}
    public function index(Request $request) { $mcu_medical_actions = $this->service->paginate($request); return view('mcu.mcu-medical-actions.index', compact('mcu_medical_actions')); }
    public function create() { return view('mcu.mcu-medical-actions.create'); }
    public function store(StoreMcuMedicalActionRequest $request) { $this->service->create($request->validated()); return redirect()->route('mcu-medical-actions.index')->with('success','Data berhasil dibuat!'); }
    public function edit(McuMedicalAction $mcuMedicalAction) { return view('mcu.mcu-medical-actions.edit', ['mcu_medical_actions'=>$mcuMedicalAction]); }
    public function update(UpdateMcuMedicalActionRequest $request, McuMedicalAction $mcuMedicalAction) { $this->service->update($mcuMedicalAction,$request->validated()); return redirect()->route('mcu-medical-actions.index')->with('success','Data berhasil diperbarui!'); }
    public function destroy(McuMedicalAction $mcuMedicalAction) { $this->service->delete($mcuMedicalAction); return redirect()->route('mcu-medical-actions.index')->with('success','Data berhasil dihapus!'); }
}
