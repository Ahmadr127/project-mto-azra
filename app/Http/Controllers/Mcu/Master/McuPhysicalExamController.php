<?php

namespace App\Http\Controllers\Mcu\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcu\Master\StoreMcuPhysicalExamRequest;
use App\Http\Requests\Mcu\Master\UpdateMcuPhysicalExamRequest;
use App\Models\McuPhysicalExam;
use App\Services\Mcu\Master\McuPhysicalExamService;
use Illuminate\Http\Request;

class McuPhysicalExamController extends Controller
{
    public function __construct(protected McuPhysicalExamService $service) {}
    public function index(Request $request) { $mcu_physical_exams = $this->service->paginate($request); return view('mcu.mcu-physical-exams.index', compact('mcu_physical_exams')); }
    public function create() { return view('mcu.mcu-physical-exams.create'); }
    public function store(StoreMcuPhysicalExamRequest $request) { $this->service->create($request->validated()); return redirect()->route('mcu-physical-exams.index')->with('success','Data berhasil dibuat!'); }
    public function edit(McuPhysicalExam $mcuPhysicalExam) { return view('mcu.mcu-physical-exams.edit', ['mcu_physical_exams'=>$mcuPhysicalExam]); }
    public function update(UpdateMcuPhysicalExamRequest $request, McuPhysicalExam $mcuPhysicalExam) { $this->service->update($mcuPhysicalExam,$request->validated()); return redirect()->route('mcu-physical-exams.index')->with('success','Data berhasil diperbarui!'); }
    public function destroy(McuPhysicalExam $mcuPhysicalExam) { $this->service->delete($mcuPhysicalExam); return redirect()->route('mcu-physical-exams.index')->with('success','Data berhasil dihapus!'); }
}
