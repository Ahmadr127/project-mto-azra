<?php

namespace App\Services\Mcu\Master;

use App\Models\McuRadiology;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class McuRadiologyService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = McuRadiology::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%{$search}%")->orWhere('code','like',"%{$search}%"));
        }
        return $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
    }
    public function create(array $data): McuRadiology { return McuRadiology::create($data); }
    public function update(McuRadiology $m, array $data): McuRadiology { $m->update($data); return $m; }
    public function delete(McuRadiology $m): void { $m->delete(); }
}
