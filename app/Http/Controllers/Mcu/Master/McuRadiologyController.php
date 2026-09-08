<?php

namespace App\Http\Controllers\Mcu\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcu\Master\StoreMcuRadiologyRequest;
use App\Http\Requests\Mcu\Master\UpdateMcuRadiologyRequest;
use App\Models\McuRadiology;
use App\Services\Mcu\Master\McuRadiologyService;
use Illuminate\Http\Request;

class McuRadiologyController extends Controller
{
    public function __construct(protected McuRadiologyService $service) {}
    public function index(Request $request) { $mcu_radiologies = $this->service->paginate($request); return view('mcu.mcu-radiologies.index', compact('mcu_radiologies')); }
    public function create() { return view('mcu.mcu-radiologies.create'); }
    public function store(StoreMcuRadiologyRequest $request) { $this->service->create($request->validated()); return redirect()->route('mcu-radiologies.index')->with('success','Data berhasil dibuat!'); }
    public function edit(McuRadiology $mcuRadiology) { return view('mcu.mcu-radiologies.edit', ['mcu_radiologies'=>$mcuRadiology]); }
    public function update(UpdateMcuRadiologyRequest $request, McuRadiology $mcuRadiology) { $this->service->update($mcuRadiology,$request->validated()); return redirect()->route('mcu-radiologies.index')->with('success','Data berhasil diperbarui!'); }
    public function destroy(McuRadiology $mcuRadiology) { $this->service->delete($mcuRadiology); return redirect()->route('mcu-radiologies.index')->with('success','Data berhasil dihapus!'); }
}
