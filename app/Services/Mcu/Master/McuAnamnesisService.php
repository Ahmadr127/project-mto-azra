<?php

namespace App\Services\Mcu\Master;

use App\Models\McuAnamnesis;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class McuAnamnesisService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = McuAnamnesis::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%{$search}%")->orWhere('code','like',"%{$search}%"));
        }
        return $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
    }
    public function create(array $data): McuAnamnesis { return McuAnamnesis::create($data); }
    public function update(McuAnamnesis $m, array $data): McuAnamnesis { $m->update($data); return $m; }
    public function delete(McuAnamnesis $m): void { $m->delete(); }
}
