<?php

namespace App\Http\Controllers\Mcu\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcu\Master\StoreMcuAnamnesisRequest;
use App\Http\Requests\Mcu\Master\UpdateMcuAnamnesisRequest;
use App\Models\McuAnamnesis;
use App\Services\Mcu\Master\McuAnamnesisService;
use Illuminate\Http\Request;

class McuAnamnesisController extends Controller
{
    public function __construct(protected McuAnamnesisService $service) {}
    public function index(Request $request) { $mcu_anamneses = $this->service->paginate($request); return view('mcu.mcu-anamneses.index', compact('mcu_anamneses')); }
    public function create() { return view('mcu.mcu-anamneses.create'); }
    public function store(StoreMcuAnamnesisRequest $request) { $this->service->create($request->validated()); return redirect()->route('mcu-anamneses.index')->with('success','Data berhasil dibuat!'); }
    public function edit(McuAnamnesis $mcuAnamnesis) { return view('mcu.mcu-anamneses.edit', ['mcu_anamneses'=>$mcuAnamnesis]); }
    public function update(UpdateMcuAnamnesisRequest $request, McuAnamnesis $mcuAnamnesis) { $this->service->update($mcuAnamnesis,$request->validated()); return redirect()->route('mcu-anamneses.index')->with('success','Data berhasil diperbarui!'); }
    public function destroy(McuAnamnesis $mcuAnamnesis) { $this->service->delete($mcuAnamnesis); return redirect()->route('mcu-anamneses.index')->with('success','Data berhasil dihapus!'); }
}
