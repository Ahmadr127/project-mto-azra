<?php

namespace App\Services\Mcu\Master;

use App\Models\McuPhysicalExam;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class McuPhysicalExamService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = McuPhysicalExam::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%{$search}%")->orWhere('code','like',"%{$search}%"));
        }
        return $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
    }
    public function create(array $data): McuPhysicalExam { return McuPhysicalExam::create($data); }
    public function update(McuPhysicalExam $m, array $data): McuPhysicalExam { $m->update($data); return $m; }
    public function delete(McuPhysicalExam $m): void { $m->delete(); }
}
