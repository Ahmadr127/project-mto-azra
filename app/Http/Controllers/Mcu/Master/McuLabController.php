<?php

namespace App\Http\Controllers\Mcu\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mcu\Master\StoreMcuLabRequest;
use App\Http\Requests\Mcu\Master\UpdateMcuLabRequest;
use App\Models\McuLab;
use App\Services\Mcu\Master\McuLabService;
use Illuminate\Http\Request;

class McuLabController extends Controller
{
    public function __construct(protected McuLabService $service) {}

    public function index(Request $request)
    {
        $mcu_labs = $this->service->paginate($request);
        return view('mcu.mcu-labs.index', compact('mcu_labs'));
    }

    public function create()
    {
        return view('mcu.mcu-labs.create');
    }

    public function store(StoreMcuLabRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('mcu-labs.index')->with('success', 'Data berhasil dibuat!');
    }

    public function edit(McuLab $mcuLab)
    {
        return view('mcu.mcu-labs.edit', ['mcu_labs' => $mcuLab]);
    }

    public function update(UpdateMcuLabRequest $request, McuLab $mcuLab)
    {
        $this->service->update($mcuLab, $request->validated());
        return redirect()->route('mcu-labs.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(McuLab $mcuLab)
    {
        $this->service->delete($mcuLab);
        return redirect()->route('mcu-labs.index')->with('success', 'Data berhasil dihapus!');
    }
}
