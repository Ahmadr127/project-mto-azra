<?php

namespace App\Services\Mcu\Master;

use App\Models\McuRadiology;
use App\Support\SearchHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class McuRadiologyService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        $query = McuRadiology::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                SearchHelper::whereLike($q, 'name', $search, 'and');
                SearchHelper::whereLike($q, 'code', $search, 'or');
            });
        }
        return $query->orderBy('display_order')->latest()->paginate(10)->withQueryString();
    }
    public function create(array $data): McuRadiology { return McuRadiology::create($data); }
    public function update(McuRadiology $m, array $data): McuRadiology { $m->update($data); return $m; }
    public function delete(McuRadiology $m): void { $m->delete(); }
}
